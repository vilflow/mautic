<?php

namespace MauticPlugin\MauticPostmarkBundle\Service;

class PostmarkApiService
{
    /**
     * Fetches server list from Postmark Account API.
     *
     * @param string $accountToken Account-level API token from Postmark
     * @param int    $offset       Starting position (default: 0)
     * @param int    $count        Number of servers to fetch (default: 100)
     *
     * @return array [success(bool), servers(array), error(string|null)]
     */
    public function getServerList(string $accountToken, int $offset = 0, int $count = 100): array
    {
        if (empty($accountToken)) {
            return [false, [], 'Account token is required'];
        }

        $url = 'https://api.postmarkapp.com/servers?offset=' . $offset . '&count=' . $count;
        $ch = curl_init($url);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Postmark-Account-Token: ' . $accountToken,
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'GET',
        ]);

        $response   = curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno      = curl_errno($ch);
        $error      = $errno ? curl_error($ch) : null;
        curl_close($ch);

        if ($errno !== 0) {
            return [false, [], 'cURL error: ' . $error];
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $decoded = json_decode((string) $response, true);
            $errorMsg = 'HTTP ' . $statusCode;
            if (is_array($decoded) && !empty($decoded['Message'])) {
                $errorMsg .= ': ' . $decoded['Message'];
            }
            return [false, [], $errorMsg];
        }

        $decoded = json_decode((string) $response, true);
        if (!is_array($decoded)) {
            return [false, [], 'Invalid JSON response'];
        }

        // Extract servers array from response
        $servers = $decoded['Servers'] ?? [];
        if (!is_array($servers)) {
            return [false, [], 'Invalid servers data in response'];
        }

        return [true, $servers, null];
    }

    /**
     * Formats server list for use in form choices.
     *
     * @param array $servers Server list from API
     *
     * @return array [server_token => 'Server Name (ID)']
     */
    public function formatServerChoices(array $servers): array
    {
        $choices = [];
        
        foreach ($servers as $server) {
            if (!is_array($server)) {
                continue;
            }
            
            $name = $server['Name'] ?? 'Unknown';
            $id = $server['ID'] ?? '';
            
            // Handle ApiTokens - could be array or string
            $token = '';
            if (!empty($server['ApiTokens'])) {
                if (is_array($server['ApiTokens']) && !empty($server['ApiTokens'])) {
                    $token = $server['ApiTokens'][0] ?? '';
                } elseif (is_string($server['ApiTokens'])) {
                    $token = $server['ApiTokens'];
                }
            }
            
            if (!empty($token)) {
                $choices[$name . ' (ID: ' . $id . ')'] = $token;
            }
        }
        
        return $choices;
    }

    /**
     * Fetches templates from Postmark Server API.
     *
     * @param string $serverToken Server token from Postmark
     * @param int    $offset      Starting position (default: 0)
     * @param int    $count       Number of templates to fetch (default: 100)
     *
     * @return array [success(bool), templates(array), error(string|null)]
     */
    public function getTemplateList(string $serverToken, int $offset = 0, int $count = 100): array
    {
        if (empty($serverToken)) {
            return [false, [], 'Server token is required'];
        }

        $url = 'https://api.postmarkapp.com/templates?offset=' . $offset . '&count=' . $count;
        $ch = curl_init($url);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Postmark-Server-Token: ' . $serverToken,
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'GET',
        ]);

        $response   = curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno      = curl_errno($ch);
        $error      = $errno ? curl_error($ch) : null;
        curl_close($ch);

        if ($errno !== 0) {
            return [false, [], 'cURL error: ' . $error];
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $decoded = json_decode((string) $response, true);
            $errorMsg = 'HTTP ' . $statusCode;
            if (is_array($decoded) && !empty($decoded['Message'])) {
                $errorMsg .= ': ' . $decoded['Message'];
            }
            return [false, [], $errorMsg];
        }

        $decoded = json_decode((string) $response, true);
        if (!is_array($decoded)) {
            return [false, [], 'Invalid JSON response'];
        }

        // Extract templates array from response
        $templates = $decoded['Templates'] ?? [];
        if (!is_array($templates)) {
            return [false, [], 'Invalid templates data in response'];
        }

        return [true, $templates, null];
    }

    /**
     * Formats template list for use in form choices.
     *
     * @param array $templates Template list from API
     *
     * @return array [template_alias => 'Template Name (Alias)']
     */
    public function formatTemplateChoices(array $templates): array
    {
        $choices = [];
        
        foreach ($templates as $template) {
            if (!is_array($template)) {
                continue;
            }
            
            $name = $template['Name'] ?? 'Unknown';
            $alias = $template['Alias'] ?? '';
            
            if (!empty($alias)) {
                $choices[$name . ' (' . $alias . ')'] = $alias;
            }
        }
        
        return $choices;
    }
}