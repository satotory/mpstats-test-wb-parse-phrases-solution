<?php

function sys__save_phrase_search_response(string $phrase, string $json): string {
    $filename = sys__phrase_search_response_filepath($phrase);

    file_put_contents($filename, $json);

    return $filename;
}

function sys__phrase_search_response_filepath(string $phrase): string {
    $folder = sprintf("%s/var/responses", APP_ROOT);
    sys__mkdir($folder);
    $filename = sprintf("%s.json", sys__get_code_by_phrase($phrase));
    
    return sprintf(
        "%s/%s",
        $folder,
        $filename  
    );
}

function sys__article_json_filepath(int $wb_article): string {
    $folder = sprintf("%s/var/articles", APP_ROOT);
    sys__mkdir($folder);
    $filename = sprintf("%s.json", $wb_article);

    return sprintf(
        "%s/%s",
        $folder,
        $filename  
    );
}

function sys__get_article_data(int $wb_article): array|null {
    $article_save_file = sys__article_json_filepath($wb_article);

    if (file_exists($article_save_file)) {
        $product_search_result = json_decode(
            file_get_contents($article_save_file), 
            true
        );   
    }

    return $product_search_result ?? null;
}

function sys__article_save_json(int $wb_article, string $json) {
    $filename = sys__article_json_filepath($wb_article);

    file_put_contents($filename, $json);
}

function sys__get_code_by_phrase(string $phrase): string {
    return base64_encode($phrase);
}

function sys__mkdir(string $dir_path): void {
    if (!is_dir($dir_path)) {
        $created = mkdir($dir_path, 0775, true);
        if (!$created) {
            throw new \Exception("Failed to create folder. Path: {$dir_path}");
        }
    }
}