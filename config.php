<?php

require_once('IyzipayBootstrap.php');

IyzipayBootstrap::init();

class Config
{
    public static function options()
    {
        $options = new \Iyzipay\Options();
        
        // Sandbox veya canlı ortam seçimi
        $is_sandbox = false; // Canlı ortama geçmek için false yapın
        
        if ($is_sandbox) {
            $options->setApiKey('sandbox-');
            $options->setSecretKey('sandbox-');
            $options->setBaseUrl('https://sandbox-api.iyzipay.com');
        } else {
            // Canlı ortam API anahtarlarınızı buraya ekleyin
            $options->setApiKey('');
            $options->setSecretKey('');
            $options->setBaseUrl('https://api.iyzipay.com');
        }

        return $options;
    }
}