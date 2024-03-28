<?php

require_once dirname(__DIR__, 1) . "/config/bootstrap.php";

if (!$argv[1]) {
    die("Необходимо задать артикул первым аргументом.");
}

$wb_article = (int) $argv[1];

$article_data = sys__get_article_data($wb_article);
if (!$article_data) {
    die("Артикул не найден.");
}

$article_data_table = (new LucidFrame\Console\ConsoleTable())
    ->addHeader('Артикул')
    ->addHeader('Поисковая фраза')
    ->addHeader('Позиция')
    ->addHeader('Последнее обновление');

foreach ($article_data[ARTICLE_DATA_PHRASES] as $data) {
    $article_data_table->addRow();

    $article_data_table
        ->addColumn($wb_article)
        ->addColumn($data[ARTICLE_DATA_PHRASES_PHRASE])
        ->addColumn($data[ARTICLE_DATA_PHRASES_POSITION])
        ->addColumn(date("d.m.y, H:i:s", $data[ARTICLE_DATA_PHRASES_LAST_UPDATE]));
}

$article_data_table->display();
