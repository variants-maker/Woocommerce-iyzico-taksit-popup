<?php
/*
Plugin Name: iyzico Taksit Seçenekleri
Plugin URI: https://github.com/pushigo/iyzicoTaksit
Description: Kredi kartı için taksit seçeneklerini ürün sayfası içerisinde gösterir. 
Version: 1.0.0
Author: Pushigo
Author URI: https://pushigo.com
License: GNU
*/

// Tab yerine doğrudan ürün detay sayfasının altına ekleyelim
add_action('woocommerce_after_single_product_summary', 'display_installment_options', 20);
function display_installment_options()
{
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Check if we're on a product page
    if (!is_product()) {
        return;
    }

    //Include external resources 
    require_once('config.php');
    require_once ('style.css');
   
    //Get product price
    global $product;
    if (!$product) {
        return;
    }

    $regular_price = esc_attr($product->get_display_price());
    if (!$regular_price) {
        return;
    }

    // The installment content
    echo '<div class="installment-section">';
    echo '<h2 class="installment-title">Taksit Seçenekleri</h2>';
    
    try {
        # create request class
        $request = new \Iyzipay\Request\RetrieveInstallmentInfoRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId(uniqid());
        $request->setPrice($regular_price);
        
        # make request
        $installmentInfo = \Iyzipay\Model\InstallmentInfo::retrieve($request, Config::options());
        
        # check response
        if ($installmentInfo->getStatus() != 'success') {
            echo '<p class="installment-error">Taksit bilgileri alınamadı. Lütfen daha sonra tekrar deneyiniz.</p>';
            return;
        }
        
        # json decode
        $result = $installmentInfo->getRawResult();
        $result = json_decode($result);
        
        if (!$result || !isset($result->installmentDetails)) {
            echo '<p class="installment-error">Taksit bilgileri işlenemedi.</p>';
            return;
        }
        
        $result = $result->installmentDetails;
        
        # create cards container
        echo '<div class="cards">';
       
        # data parsing 
        foreach ($result as $key => $dataParser) {
            if (!isset($dataParser->cardFamilyName) || !isset($dataParser->installmentPrices)) {
                continue;
            }
            
            $bankName = $dataParser->cardFamilyName;
            echo '<div class="card card--' . esc_attr($bankName) . '">';
            echo '<div class="card__head">' . esc_html($bankName) . '</div>';
            echo '<div class="card__content">';
            
            echo '<table class="card__table">';
            echo '<thead><tr>';
            echo '<th>Taksit</th>';
            echo '<th>Tutar</th>';
            echo '<th>Toplam</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            
            foreach ($dataParser->installmentPrices as $installment) {
                if (!isset($installment->installmentNumber) || !isset($installment->installmentPrice) || !isset($installment->totalPrice)) {
                    continue;
                }
                
                echo '<tr>';
                echo '<td>' . esc_html($installment->installmentNumber) . '</td>';
                echo '<td>' . number_format($installment->installmentPrice, 2, ',', '.') . ' TL</td>';
                echo '<td>' . number_format($installment->totalPrice, 2, ',', '.') . ' TL</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            echo '</div></div>';
        }
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<p class="installment-error">Taksit bilgileri alınırken bir hata oluştu: ' . esc_html($e->getMessage()) . '</p>';
    }
    
    echo '</div>';
}

// Taksit butonunu ekle
remove_action('woocommerce_after_add_to_cart_button', 'add_installment_button');
add_action('woocommerce_after_add_to_cart_button', 'custom_cart_buttons_and_installment');
function custom_cart_buttons_and_installment() {
    ?>
    <div class="cart-buttons-row">
        <button type="button" class="installment-button" onclick="showInstallmentPopup()">Taksit Seçenekleri</button>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth <= 480) { // Sadece mobilde uygula
            var rowDiv = document.querySelector('.cart-buttons-row');
            var addToCartBtn = document.querySelector('.single_add_to_cart_button');
            var installmentBtn = document.querySelector('.installment-button');
            if (rowDiv && addToCartBtn && installmentBtn) {
                // Sepete Ekle butonunun yanına taksit butonunu ekle
                rowDiv.innerHTML = '';
                rowDiv.appendChild(addToCartBtn);
                rowDiv.appendChild(installmentBtn);
            }
        }
    });
    </script>
    <?php
}

// Popup içeriğini ekle
add_action('wp_footer', 'add_installment_popup');
function add_installment_popup() {
    if (!is_product()) {
        return;
    }

    //Get product price
    global $product;
    if (!$product) {
        return;
    }

    $regular_price = esc_attr($product->get_display_price());
    if (!$regular_price) {
        return;
    }

    //Include external resources 
    require_once('config.php');
    require_once ('style.css');
    
    ?>
    <div id="installmentPopup" class="installment-popup">
        <div class="installment-popup-content">
            <span class="installment-popup-close" onclick="hideInstallmentPopup()">&times;</span>
            <h2 class="installment-title">Taksit Seçenekleri</h2>
            <?php
            try {
                # create request class
                $request = new \Iyzipay\Request\RetrieveInstallmentInfoRequest();
                $request->setLocale(\Iyzipay\Model\Locale::TR);
                $request->setConversationId(uniqid());
                $request->setPrice($regular_price);
                
                # make request
                $installmentInfo = \Iyzipay\Model\InstallmentInfo::retrieve($request, Config::options());
                
                # check response
                if ($installmentInfo->getStatus() != 'success') {
                    echo '<p class="installment-error">Taksit bilgileri alınamadı. Lütfen daha sonra tekrar deneyiniz.</p>';
                    return;
                }
                
                # json decode
                $result = $installmentInfo->getRawResult();
                $result = json_decode($result);
                
                if (!$result || !isset($result->installmentDetails)) {
                    echo '<p class="installment-error">Taksit bilgileri işlenemedi.</p>';
                    return;
                }
                
                $result = $result->installmentDetails;
                
                # create cards container
                echo '<div class="cards">';
               
                # data parsing 
                foreach ($result as $key => $dataParser) {
                    if (!isset($dataParser->cardFamilyName) || !isset($dataParser->installmentPrices)) {
                        continue;
                    }
                    
                    $bankName = $dataParser->cardFamilyName;
                    echo '<div class="card card--' . esc_attr($bankName) . '">';
                    echo '<div class="card__head">' . esc_html($bankName) . '</div>';
                    echo '<div class="card__content">';
                    
                    echo '<table class="card__table">';
                    echo '<thead><tr>';
                    echo '<th>Taksit</th>';
                    echo '<th>Tutar</th>';
                    echo '<th>Toplam</th>';
                    echo '</tr></thead>';
                    echo '<tbody>';
                    
                    foreach ($dataParser->installmentPrices as $installment) {
                        if (!isset($installment->installmentNumber) || !isset($installment->installmentPrice) || !isset($installment->totalPrice)) {
                            continue;
                        }
                        
                        echo '<tr>';
                        echo '<td>' . esc_html($installment->installmentNumber) . '</td>';
                        echo '<td>' . number_format($installment->installmentPrice, 2, ',', '.') . ' TL</td>';
                        echo '<td>' . number_format($installment->totalPrice, 2, ',', '.') . ' TL</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                    echo '</div></div>';
                }
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<p class="installment-error">Taksit bilgileri alınırken bir hata oluştu: ' . esc_html($e->getMessage()) . '</p>';
            }
            ?>
        </div>
    </div>
    <script>
        function showInstallmentPopup() {
            document.getElementById('installmentPopup').style.display = 'block';
        }
        
        function hideInstallmentPopup() {
            document.getElementById('installmentPopup').style.display = 'none';
        }
        
        // Popup dışına tıklandığında kapat
        window.onclick = function(event) {
            var popup = document.getElementById('installmentPopup');
            if (event.target == popup) {
                popup.style.display = 'none';
            }
        }
    </script>
    <?php
}

// Orijinal taksit gösterimini gizle
add_action('wp_head', 'hide_original_installment');
function hide_original_installment() {
    if (is_product()) {
        ?>
        <style>
            .installment-section {
                display: block !important;
            }
        </style>
        <?php
    }
}
?>