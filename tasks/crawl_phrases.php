<?php

require_once dirname(__DIR__, 1) . "/config/bootstrap.php";

$search_phrases = [
    "футболка оверсайз",
    "футболка мужская",
    "футболка мужская оверсайз",
];

foreach ($search_phrases as $phrase) {
    $crawling_time = time();

    $products = swb__get_products_by_phrase($phrase);

    foreach ($products as $index => $product) {
        $wb_article     = $product['id'];
        $position       = $index + 1;
        $phrase_code    = sys__get_code_by_phrase($phrase);
        
        $default_article_data = [
            ARTICLE_DATA_ARTICLE => $wb_article,
            ARTICLE_DATA_PHRASES => [],
        ];

        $article_data = sys__get_article_data($wb_article) ?? $default_article_data;

        $phrase_article_data = [
            ARTICLE_DATA_PHRASES_PHRASE => $phrase,
            ARTICLE_DATA_PHRASES_LAST_UPDATE => $crawling_time,
            ARTICLE_DATA_PHRASES_POSITION => $position,
        ];

        $article_data[ARTICLE_DATA_PHRASES][$phrase_code] = $phrase_article_data;

        sys__article_save_json(
            $wb_article, 
            json_encode($article_data)
        );
    }
}
