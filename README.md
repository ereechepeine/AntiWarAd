# Антивоенная вставка для вебсайтов

Эту вставку можно использовать вместо большого баннера. Там часто будут актуальные кликбейтные заголовки. При каждом обновлении страницы будет предложено новое видео из списка каналов YouTube из `yt_dyn_embed.php`. Предложенные видео сохраняются в куки из JavaScript.

Для корректной работы необходим cURL, и крайне желательно иметь установленный APCu.

Если будет интерес - могу это переделать в виде JavaScript, который можно будет вставить в любой сайт. Если надо - пишите в [issues](https://github.com/ereechepeine/AntiWarAd/issues).

## Пример

Достаточно заинклудить файл `yt_dyn_embed.php` в любом месте в шаблоне PHP и вставка будет подгружаться автоматически.

```php
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <title>Тест</title>
        <style>
            body {
                text-align: center;
                margin-top: 5em;
            }
        </style>
    </head>
    <body>
        <?php require 'yt_dyn_embed.php'; ?>
    </body>
</html>
```

## Контакты

Если есть вопросы или пожелания - пишите в [issues](https://github.com/ereechepeine/AntiWarAd/issues). Вероятно он сможет передать их.