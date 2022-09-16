# LogisticaBundle

Bundle com API para pesquisar endereços, cidades e CEP.

## endpoints:

*/logradouro/cep/{cep}*:
> Métodos aceitos: GET  
> Parâmetro obrigatório: cep  
> Retorno: Array de logradouros que possuem o cep passado como parâmetro
> Alias do path: nte_aplicacao_logistica_logradouro_cep 

*/logradouro/termo/{termo}*:
> Métodos aceitos: GET  
> Parâmetro obrigatório: termo a ser pesquisado
> Parâmetros opcionais pela query:
>   - localidade: Parte do nome do município ou nome completo.
>   - bairro: Parte do nome do bairro ou nome completo.
>   - uf: Unidade federativa.
>    
> Retorno: Array de logradouros que possuem o termo pesquisado em qualquer um de seus atributos  
> Alias do path: nte_aplicacao_logistica_logradouro_termo

*/localidade/termo/{termo}*:
> Métodos aceitos: GET  
> Parâmetro obrigatório: termo a ser pesquisado  
> Parâmetros opcionais pela query:
>   - uf: Unidade federativa.
>    
> Retorno: Array de localidades que possuem o termo passado no nome
> Alias do path: nte_aplicacao_logistica_localidade_termo  
