<?php

// This program is free software: you can redistribute it and/or modify
// it without ANY limitations. Feel free to do so.

function yt_get_embed_urls($channel_ids) {
    if (!is_array($channel_ids)) {
        if (is_string($channel_ids)) {
            $channel_ids = array($channel_ids);
        } else {
            return false;
        }
    }

    $urls = yt_cache('youtube_m_' . implode('-', $channel_ids), function() use ($channel_ids) {
        $mh = curl_multi_init();

        $channels = array();

        foreach ($channel_ids as $channel_id) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.youtube.com/feeds/videos.xml?channel_id=" . $channel_id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_multi_add_handle($mh, $ch);
            $channels[] = $ch;
        }

        $active = null;

        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        $channel_urls = array();
        $max = 0;

        foreach ($channels as $i => $ch) {
            $rss = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);

            preg_match_all('/<yt:videoId>([^<]+)<\/yt:videoId>/m', $rss, $matches, PREG_SET_ORDER, 0);
            foreach ($matches as $match) {
                $channel_urls[$i][] = "https://www.youtube.com/embed/$match[1]";
                $max = max($max, count($channel_urls[$i]));
            }
        }

        curl_multi_close($mh);

        $urls = array();

        for ($i = 0; $i < $max; $i++) {
            foreach ($channel_urls as $channel_url) {
                if (isset($channel_url[$i])) {
                    $urls[] = $channel_url[$i];
                }
            }
        }

        return $urls;
    }, 300);

    return $urls;
}

function yt_cache($key, $generator, $max_age = 300) {
    if (function_exists('apcu_entry') && apcu_enabled()) {
        return unserialize(apcu_entry("$key.s", function() use($generator) {
            return serialize($generator());
        }, $max_age));
    }
    
    $cache_prefix =  sys_get_temp_dir() . DIRECTORY_SEPARATOR;

    @mkdir($cache_prefix, 0700, true);

    $cache_file = $cache_prefix . $key . '.phpcache';

    if (file_exists($cache_file) && filemtime($cache_file) > time() - $max_age) {
        return unserialize(file_get_contents($cache_file));
    }

    $data = $generator();

    file_put_contents($cache_file, serialize($data));

    return $data;
}

$v_urls = yt_get_embed_urls(
    array(
        'UCwxkg8SN6OHONwcJQb-O4WQ', // Острый угол
        'UCdubelOloxR3wzwJG9x8YqQ', // Дождь
        'UCUGfDbfRIx51kJGGHIFo8Rw', // Кац
        // 'UC7Elc-kLydl-NAV4g204pDQ', // Популярная политика
        // 'UCCROBQj3rdGNc-E_aMpbV_A', // MyGAP
        'UCr1QVPhsNT9cMI8FXAbzlig', // Объектив
        // 'UCAg74TJrwfpuCp1Jo3ZHMjA', // Михаил Ходорковский
        'UC101o-vQ2iOj9vr00JUlyKw', // Варламов
        // 'UCBG57608Hukev3d0d-gvLhQ', // Настоящее Время
        // 'UCgpSieplNxXxLXYAzJLLpng', // Майкл Наки
    )
);

?>
<div id="ciiZoga"></div>
<script>
    let wd = document.getElementById('ciiZoga');
    
    let attrs = {
        onerror: "onYouTubeNotAvail()",
        width: "560",
        height: "315",
        title: "YouTube video player",
        frameborder: "0",
        allow: "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture",
        allowfullscreen: ""
    };
    
    let urls = <?= json_encode($v_urls) ?>;
    
    let rand = Math.random() * urls.length;
    let randIndex = Math.floor(Math.sqrt(rand * rand));
    attrs.src = urls[urls.length - 1 - randIndex];

    for(const url of urls) {
        if (!localStorage.getItem('seen:' + url)) {
            localStorage.setItem('seen:' + url, true);
            attrs.src = url;
            break;
        }
    }
    
    function onYouTubeNotAvail() {
        wd.innerHTML = '<a href="https://readli.net/1984-2/"><img src="1984-cover.jpg" style="height: 45em"></a>';
    }

    if (attrs.src) {
        let iframe = document.createElement('IFRAME');

        for(const attr in attrs) {
            iframe.setAttribute(attr, attrs[attr]);
        }
        
        wd.appendChild(iframe);

        let intId = setInterval(() => {
            if (iframe.parentNode === null) {
                clearInterval(intId);
                onYouTubeNotAvail();
                return;
            }
        }, 100);
    } else {
        onYouTubeNotAvail();
    }
</script>
