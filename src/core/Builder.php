<?php


namespace MJ\HTML;


class Builder
{

    public static function loadStylesheet($stylesheets)
    {
        global $Settings;
        foreach ((array)$stylesheets as $stylesheet) {
            ?>
            <link href="<?= $stylesheet['src'] ?>?v=<?= $Settings['site_version'] ?>" rel="<?= $stylesheet['rel'] ?>" type="<?= $stylesheet['type'] ?>"
                  id="<?= $stylesheet['name'] ?>">
            <?php
        }
    }


    public static function loadStylesheet___________($stylesheets, $slug)
    {
        global $Settings;
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $slug = $slug . '.css';
        if (!file_exists(getcwd() . '\dist\css\min\\' . $slug)) {
            $output = '';
            foreach ((array)$stylesheets as $stylesheet) {
                $output .= file_get_contents(SITE_URL . $stylesheet['src'], false, stream_context_create($arrContextOptions));
            }
            file_put_contents(getcwd() . '\dist\css\min\\' . $slug, $output);
            ?>
            <link href="/dist/css/min/<?= $slug ?>?v=<?= $Settings['site_version'] ?>" rel="stylesheet" type="text/css"
                  id="<?= $slug ?>">
            <?php
        } else {
            ?>
            <link href="/dist/css/min/<?= $slug ?>?v=<?= $Settings['site_version'] ?>" rel="stylesheet" type="text/css"
                  id="<?= $slug ?>">
            <?php
        }

    }


    public static function loadScripts($scripts)
    {
        global $Settings;
        foreach ((array)$scripts as $script) {
            ?>
            <script src="<?= $script['src'] ?>?v=<?= $Settings['site_version'] ?>"" type="<?= $script['type'] ?>" id="<?= $script['name'] ?>"></script>
            <?php
        }
   
    }

    function minify_js($js)
    {
        $js = preg_replace('/\/\*[\s\S]*?\*\/|\/\/.*?[\r\n]/', '', $js); // Remove comments
        $js = preg_replace('/\s+/', ' ', $js); // Replace multiple spaces with single space
        $js = preg_replace('/\s*([+={},;:()|&])\s*/', '$1', $js); // Remove spaces around certain characters
        return $js;
    }

    function minify_css($css)
    {
        $css = preg_replace('/\/\*[\s\S]*?\*\/|[\r\n]+/', '', $css); // Remove comments and newlines
        $css = preg_replace('/\s*([{}|>:;,])\s*/', '$1', $css); // Remove spaces around certain characters
        return $css;
    }

}