<?php

function swb__uri(): string {
    $protocol = "https";
    $domain = "search.wb.ru";

    return sprintf(
        "%s://%s",
        $protocol,
        $domain,
    );
}

function swb__search_uri(array $getParams = []): string {
    $path   = "exactmatch/ru/common";
    $v      = "v5";
    $method = "search";

    return sprintf(
        "%s/%s/%s/%s?%s",
        swb__uri(),
        $path,
        $v,
        $method,
        http_build_query($getParams)
    );
}

function swb__phrase_search(string $phrase): Psr\Http\Message\ResponseInterface {
    $search_get_params = [
        'query'         => $phrase,
        'ab_testing'    => false,
        'appType'       => 1,
        'curr'          => 'rub',
        'dest'          => '-1257786',
        'resultset'     => 'catalog',
        'sort'          => 'popular',
        'spp'           => 30,
        'suppressSpellcheck' => false,
    ];

    $client = new GuzzleHttp\Client([
        "timeout"   => 2,
    ]);
    
    $fakeHeaders = new Aszone\FakeHeaders\FakeHeaders;
    $user_agent = $fakeHeaders->getUserAgent();
    
    $request = new GuzzleHttp\Psr7\Request(
        "GET",
        swb__search_uri($search_get_params),
        $user_agent
    );

    $response = $client->sendRequest($request);

    return $response;
}

function swb__get_phrase_search_results(string $phrase): array {
    $response = swb__phrase_search($phrase);
    
    $search_result_json = $response->getBody()->getContents();
    
    sys__save_phrase_search_response(
        $phrase, 
        $search_result_json
    );
    
    $search_result = json_decode($search_result_json, true);

    return $search_result ?? [];
}

function swb__get_products_by_phrase(string $phrase): array {
    $crawl_success  = false;
    $crawl_tries    = 0;

    while (!$crawl_success || $crawl_tries == CRAWL_LIMIT) {
        $search_results = swb__get_phrase_search_results($phrase);

        if (count($search_results['data']['products'] ?? []) >= 99) {
            $crawl_success = true;
        } else {
            sleep(CRAWL_TIMEOUT);
        }

        $crawl_tries++;
    }

    return $search_results['data']['products'] ?? [];
}