<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Curl\Curl;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {

        $URL_BASE = 'https://edge.cpd.ufsm.br/moodle-ws/webservice/';
        /*
         * Autenticação
         */
        $a_dados_a = json_encode([
            'login' => '02403053023',
            'senha' => 'teste001',
        ]);
        $a_dados_b = json_encode([
            'login' => '02403053023',
            'senha' => 'teste01',
        ]);

        $a = new Curl();
        $a->setHeader('Content-Type', 'application/json');
        $a->setHeader('X-UFSM-Device-ID', 'eadvm60');
        $a->setUserAgent('NTE FROM HELL');
        $a->post($URL_BASE . 'autenticacao.json', $a_dados_a);

        //Testa usuario autenticado
        $response_a = json_decode($a->response);
        $this->assertEquals(200, $a->http_status_code);
        $this->assertEquals(false, $response_a->error);
        $this->assertEquals(true, in_array('Content-Type: application/json;charset=UTF-8', $a->response_headers));
        $this->assertEquals(true, is_int($response_a->id));
        $this->assertEquals(true, isset($response_a->token));
        $this->assertEquals(true, is_object($response_a->pessoa));
        $this->assertEquals(true, is_object($response_a->usuario));


        //Testa usuário não autenticado;
        $a->post($URL_BASE . 'autenticacao.json', $a_dados_b);
        $response_ab = json_decode($a->response);
        $this->assertEquals(200, $a->http_status_code);
        $this->assertEquals(503, $response_ab->error);
        $this->assertEquals(true, in_array('Content-Type: application/json;charset=UTF-8', $a->response_headers));
        $this->assertContains('Usuário não autenticado',$response_ab->mensagem );


        /**
         * Detalhes de uma pessoa
         */
        $b = new Curl();
        $b->setHeader('Content-Type', 'application/json');
        $b->setUserAgent('NTE FROM HELL');
        $b->setHeader('X-UFSM-Device-ID', 'eadvm60');
        $b->setHeader('X-UFSM-Access-Token', $response_a->token);
        $b->post($URL_BASE . 'detalhesPessoa.json');
        $response_b = json_decode($b->response);

        //$this->assertEquals('ISO-8859-1', mb_detect_encoding($b->response,'ISO-8859-1'));
        $this->assertEquals(200, $b->http_status_code);
        $this->assertEquals(false, $response_b->error);
        $this->assertEquals(true, is_int($response_b->id));
        $this->assertEquals(true, in_array('Content-Type: application/json;charset=UTF-8', $b->response_headers));
        $this->assertEquals(true, is_array($response_b->documentos));
        $this->assertEquals(true, is_array($response_b->vinculos));
        $this->assertEquals(true, is_array($response_b->usuarios));

        //Testa os detalhes de uma pessoa com Token errado
        $b = new Curl();
        $b->setHeader('Content-Type', 'application/json');
        $b->setUserAgent('NTE FROM HELL');
        $b->setHeader('X-UFSM-Device-ID', 'eadvm60');
        $b->setHeader('X-UFSM-Access-Token', 'xxxx');
        $b->post($URL_BASE . 'detalhesPessoa.json');
        $response_b = json_decode($b->response);
        $this->assertEquals(200, $b->http_status_code);
        $this->assertEquals(true, $response_b->error);
        $this->assertContains("Token inválido", $response_b->mensagem);


        /**
         * Informações do documento de uma pessoa
         */
        $c_dados = json_encode([
            'sigla' => 'CPF',
            'numero' => '024.030.530-23',
        ]);

        $c = new Curl();
        $c->setHeader('Content-Type', 'application/json');
        $c->setUserAgent('NTE FROM HELL');
        $c->setHeader('X-UFSM-Device-ID', 'eadvm60');
        $c->post($URL_BASE . 'detalhesPessoaDocumento.json', $c_dados);

        $this->assertEquals(200, $c->http_status_code);
        $this->assertEquals(true, in_array('Content-Type: application/json;charset=UTF-8', $c->response_headers));


        /*
         * Fotos de uma pessoa
         */
        $d_dados = json_encode([
            'tipo' => ['sigla' => 'G'],
            'matricula' => '2015520167',
        ]);
        $d = new Curl();
        $d->setUserAgent('NTE FROM HELL');
        $d->setHeader('Content-Type', 'application/json');
        $d->setHeader('X-UFSM-Device-ID', 'eadvm60');
        $d->setHeader('X-UFSM-Access-Token', $response_a->token);
        $d->post($URL_BASE . 'fotoPessoa.json', $d_dados);
        $this->assertEquals(200, $d->http_status_code);
        $this->assertEquals(true, in_array('Content-Type: application/json;charset=UTF-8', $d->response_headers));
    }
}
