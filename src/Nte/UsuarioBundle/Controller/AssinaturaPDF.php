<?php

/*
 * This file is part of  Friga - https://nte.ufsm.br/friga.
 * (c) Friga
 * Friga is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Friga is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Friga.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Nte\UsuarioBundle\Controller;

trait AssinaturaPDF
{
    /**
     * @return string
     */
    public function der2pem($der_data)
    {
        $pem = \chunk_split(\base64_encode($der_data), 64, "\n");
        $pem = "-----BEGIN CERTIFICATE-----\n" . $pem . "-----END CERTIFICATE-----\n";

        return $pem;
    }

    /**
     * @return array
     */
    public function extract_pkcs7_signatures($path_to_pdf)
    {
        $pdf_contents = \file_get_contents($path_to_pdf);
        $regexp = '/ByteRange\ \[\s*(\d+) (\d+) (\d+)/';
        $result = [];
        \preg_match_all($regexp, $pdf_contents, $result);
        $signatures = [];
        if (isset($result[0])) {
            $signature_count = \count($result[0]);
            for ($s = 0; $s < $signature_count; ++$s) {
                $start = $result[2][$s];
                $end = $result[3][$s];
                $signature = null;
                if ($stream = \fopen($path_to_pdf, 'rb')) {
                    $signature = \stream_get_contents($stream, $end - $start - 2, $start + 1);
                    \fclose($stream);
                    $signature = \hex2bin($signature);
                    $signatures[] = $signature;
                }
            }
        }

        return $signatures;
    }

    /**
     * @return array
     */
    public function who_signed($path_to_pdf)
    {
        $signers = [];

        $pkcs7_der_signatures = $this->extract_pkcs7_signatures($path_to_pdf);
        if (!empty($pkcs7_der_signatures)) {
            $parsed_certificates = [];
            foreach ($pkcs7_der_signatures as $pkcs7_der_signature) {
                $pkcs7_pem_signature = $this->der2pem($pkcs7_der_signature);
                $pem_certificates = [];
                $result = \openssl_pkcs7_read($pkcs7_pem_signature, $pem_certificates);
                if ($result) {
                    foreach ($pem_certificates as $pem_certificate) {
                        $parsed_certificate = \openssl_x509_parse($pem_certificate);
                        $parsed_certificates[] = $parsed_certificate;
                    }
                }
            }

            // Remove certificate authorities certificates
            $people_certificates = [];
            foreach ($parsed_certificates as $certificate_a) {
                $is_authority = false;
                foreach ($parsed_certificates as $certificate_b) {
                    if ($certificate_a['subject'] == $certificate_b['issuer']) {
                        // If certificate A is of the issuer of certificate B, then
                        // certificate A belongs to a certificate authority and,
                        // therefore, should be ignored
                        $is_authority = true;
                        break;
                    }
                }
                if (!$is_authority) {
                    $people_certificates[] = $certificate_a;
                }
            }
            // Remove duplicate certificates
            $distinct_certificates = [];
            foreach ($people_certificates as $certificate_a) {
                $is_duplicated = false;
                if (\count($distinct_certificates) > 0) {
                    foreach ($distinct_certificates as $certificate_b) {
                        if (
                            ($certificate_a['subject'] == $certificate_b['subject'])
                            && ($certificate_a['serialNumber'] == $certificate_b['serialNumber'])
                            && ($certificate_a['issuer'] == $certificate_b['issuer'])
                        ) {
                            // If certificate B has the same subject, serial number
                            // and issuer as certificate A, then certificate B is a
                            // duplicate and, therefore, should be ignored
                            $is_duplicated = true;
                            break;
                        }
                    }
                }
                if (!$is_duplicated) {
                    $distinct_certificates[] = $certificate_a;
                }
            }
            foreach ($distinct_certificates as $certificate) {
                $signers[] = $certificate['subject']['CN'];
            }
        }

        return $signers;
    }
}
