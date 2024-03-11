<?php

return [
    'shop' => [
        'layouts' => [
            'become-seller'   => 'Satıcı Ol',
            'marketplace'     => 'Pazar Yeri',
            'profile'         => 'Profil',
            'dashboard'       => 'Kontrol Paneli',
            'products'        => 'Ürünler',
            'product-reviews' => 'Ürün İncelemeleri',
            'orders'          => 'Siparişler',
            'reviews'         => 'Değerlendirmeler',
            'transactions'    => 'İşlemler',
            'sell'            => 'Satış Yap',
            'sellerFlag'      => 'Satıcı Bayrağı Nedeni',
            'productFlag'     => 'Ürün Bayrağı Nedeni',
            'earnings'        => 'Kazançlar',
            'customers'       => 'Müşteriler',
            'seller-category' => 'Satıcı Kategorileri',
            'paymentRequest'  => 'Ödeme Detayları'
        ],

        'dashboard' => [
            'total-payout'     => 'Toplam Ödeme',
            'remaining-payout' => 'Kalan Ödeme',
            'total-revenue'    => 'Toplam Gelir',
            'average-revenue'  => 'Ortalama Gelir',
        ],

        'marketplace' => [
            'already_selling'               => 'Bu ürün zaten satılıyor.',
            'this_product_is_not_in_our_db' => 'Bu ürün veritabanımızda mevcut değil.',
            'product_not_allowed'           => ':producttype ürünü satmak için izin verilmiyor.',
            'something_went_wrong'          => 'Bir şeyler yanlış gitti, lütfen tekrar deneyin.',
            'title'                         => 'Hobinizi Bir İşe Dönüştürün',
            'open-shop-label'               => 'Hemen Mağaza Açın',
            'features'                      => 'Dikkat Çekici Özellikler',
            'features-info'                 => 'Online bir işe mi başlamak istiyorsunuz? Herhangi bir karar vermeden önce, benzersiz özelliklerimizi kontrol edin.',
            'popular-sellers'               => 'Popüler Satıcılar',
            'setup-title'                   => 'Kurulumu Gerçekten Kolay',
            'setup-info'                    => 'Bir e-ticaret mağazası kurmak ve özelleştirmek gerçekten çok kolaydır.',
            'setup-1'                       => 'Hesap Oluşturun',
            'setup-2'                       => 'Mağaza Detaylarınızı Ekleyin',
            'setup-3'                       => 'Profilinizi Özelleştirin',
            'setup-4'                       => 'Ürünleri Ekleyin',
            'setup-5'                       => 'Ürünlerinizi Satmaya Başlayın',
            'open-shop-info'                => 'Bizimle birlikte çevrimiçi mağazanızı açın ve milyonlarca alıcıyla yeni dünyayı keşfedin.',
        ],

        'minimum-order' => [
            'min-order' => ':shop için minimum sipariş miktarı :amount olarak belirlendi'
        ],

        'sellers' => [
            'seller-not-approve'                  => 'Satıcı onaylanmamıştır.',
            'you-can-not-send-query-to-your-self' => 'Kendinize sorgu gönderemezsiniz.',
            'email-sent-success-msg'              => 'E-posta başarıyla gönderildi. Satıcı en kısa sürede sizinle iletişime geçecektir.',
            'email-sent-success-msg-from-admin'   => 'E-posta başarıyla gönderildi. Yönetici en kısa sürede sizinle iletişime geçecektir.',

            'account' => [
                'signup' => [
                    'want-to-be-seller'      => 'Satıcı / Tedarikçi Olmak İster Misiniz?',
                    'shop_url'               => 'Mağaza Slug\'ı Ekle',
                    'yes'                    => 'Evet',
                    'no'                     => 'Hayır',
                    'shop_url_available'     => 'Mağaza slug\'ı uygun durumda.',
                    'shop_url_not_available' => 'Mağaza slug\'ı uygun durumda değil.',
                    'become-seller'          => 'Satıcı Ol',
                    'signup-page-title'      => 'Yeni Satıcı Hesabı Oluştur',
                    'seller-registration'    => 'Satıcı Kaydı',
                    'wanna-be-seller'        => 'Satıcı Olmak İster Misin?',
                    'as-customer'            => 'Müşteri olarak kaydol',
                ],

                'customer' => [
                    'title' => 'Müşteriler'
                ],

                'profile' => [
                    'allow_product_type'        => 'İzin Verilen Ürün Türü',
                    'create-title'              => 'Satıcı Ol',
                    'file-size-2mb'             => 'Maksimum dosya boyutu 2 MB',
                    'file-size-6mb'             => 'Maksimum dosya boyutu 6 MB',
                    'edit-title'                => 'Satıcı Profilini Düzenle',
                    'url'                       => 'Mağaza Slug\'ı Ekle',
                    'save-btn-title'            => 'Kaydet',
                    'view-collection-page'      => 'Koleksiyon sayfasını görüntüle',
                    'view-seller-page'          => 'Satıcı Sayfasını Görüntüle',
                    'waiting-for-approval'      => 'Yöneticinin onayını bekliyor',
                    'revoke'                    => 'İptal Et',
                    'revoke-success'            => 'Talebiniz başarıyla iptal edildi!',
                    'general'                   => 'Genel',
                    'shop_title'                => 'Mağaza Başlığı',
                    'tax_vat'                   => 'Vergi/TCKN Numarası',
                    'phone'                     => 'İletişim Numarası',
                    'address1'                  => 'Adres 1',
                    'address2'                  => 'Adres 2',
                    'city'                      => 'Şehir',
                    'state'                     => 'Eyalet',
                    'country'                   => 'Ülke',
                    'postcode'                  => 'Posta Kodu',
                    'media'                     => 'Medya',
                    'logo'                      => 'Logo',
                    'banner'                    => 'Banner',
                    'add-image-btn-title'       => 'Resim Ekle',
                    'about'                     => 'Mağaza Hakkında',
                    'social_links'              => 'Sosyal Bağlantılar',
                    'twitter'                   => 'Twitter Kullanıcı Adı',
                    'facebook'                  => 'Facebook Kullanıcı Adı',
                    'youtube'                   => 'Youtube Kullanıcı Adı',
                    'instagram'                 => 'Instagram Kullanıcı Adı',
                    'skype'                     => 'Skype Kullanıcı Adı',
                    'linked_in'                 => 'Linked In Kullanıcı Adı',
                    'pinterest'                 => 'Pinterest Kullanıcı Adı',
                    'policies'                  => 'Politikalar',
                    'return_policy'             => 'İade Politikası',
                    'shipping_policy'           => 'Kargo Politikası',
                    'privacy_policy'            => 'Gizlilik Politikası',
                    'seo'                       => 'SEO',
                    'meta_title'                => 'Meta Başlık',
                    'meta_description'          => 'Meta Açıklama',
                    'meta_keywords'             => 'Meta Anahtar Kelimeler',
                    'minimum_amount'            => 'Minimum Sipariş Tutarını Belirle',
                    'min_order_amount'          => 'Minimum Tutar',
                    'google_analytics'          => 'Google Analytics',
                    'google_analytics_id'       => 'Google Analytics Kimliği',
                    'admin-commission'          => 'Yönetici Komisyonu',
                    'admin-commission-percent'  => 'Yönetici Komisyonu (Yüzde)',
                    'profile-background'        => 'Mağaza Arkaplanı',

                    'validation' => [
                        'logo'       => 'Lütfen satıcı profilinizin logosunu doldurunuz',
                        'shop_title' => 'Lütfen satıcı profilinizin mağaza başlığını doldurunuz',
                        'address1'   => 'Lütfen satıcı profilinizin adres 1 bilgisini doldurunuz',
                        'city'       => 'Lütfen satıcı profilinizin şehir bilgisini doldurunuz',
                        'state'      => 'Lütfen satıcı profilinizin eyalet bilgisini doldurunuz',
                        'phone'      => 'Lütfen satıcı profilinizin telefon bilgisini doldurunuz',
                        'country'    => 'Lütfen satıcı profilinizin ülke bilgisini doldurunuz',
                        'postcode'   => 'Lütfen satıcı profilinizin posta kodu bilgisini doldurunuz',
                        'image-type' => 'Yalnızca resimlere (.jpg, .jpeg, .png, .gif, .webp) izin verilir.'
                    ]
                ],

                'dashboard' => [
                    'title'             => 'Gösterge Paneli',
                    'sales-by-location' => 'Konuma Göre Satışlar'
                ],

                'earning' => [
                    'title'            => 'Kazanç',
                    'period'           => 'Dönem',
                    'start-date'       => 'Başlangıç Tarihi',
                    'end-date'         => 'Bitiş Tarihi',
                    'show-report'      => "Raporu Göster",
                    'interval'         => "Aralık",
                    'orders'           => "Siparişler",
                    'total-amt'        => "Toplam Tutar",
                    'total-earning'    => "Toplam Kazanç",
                    'total-discount'   => "Toplam İndirim",
                    'admin-commission' => "Yönetici Komisyonu",
                ],

                'catalog' => [
                    'products' => [
                        'title'             => 'Ürünler',
                        'create'            => 'Oluştur',
                        'create-new'        => 'Yeni Oluştur',
                        'search-title'      => 'Ürün Ara',
                        'create-title'      => 'Ürün Ekle',
                        'assing-title'      => 'Sizininkini Sat',
                        'assing-edit-title' => 'Ürünü Düzenle',
                        'edit-title'        => 'Ürünü Düzenle',
                        'save-btn-title'    => 'Kaydet',
                        'assign-info'       => 'Ürünleri arayın, eğer ürün varsa farklı bir fiyatla satabilirsiniz veya :create_link',
                        'search'            => 'Ara',
                        'search-term'       => 'Ürün Adı ...',
                        'no-result-found'   => 'Bu isimle ürün bulunamadı.',
                        'enter-search-term' => 'En az 3 karakter yazın',
                        'searching'         => 'Aranıyor ...',
                        'general'           => 'Genel',
                        'product-condition' => 'Ürün Durumu',
                        'new'               => 'Yeni',
                        'old'               => 'Eski',
                        'price'             => 'Fiyat',
                        'description'       => 'Açıklama',
                        'meta_description'  => 'Meta Açıklama',
                        'shipping'          => 'Kargo',
                        'images'            => 'Resimler',
                        'inventory'         => 'Envanter',
                        'variations'        => 'Çeşitlilik',
                        'id'                => 'Id',
                        'sku'               => 'Stok Kodu',
                        'name'              => 'İsim',
                        'product-number'    => 'Ürün Numarası',
                        'quantity'          => 'Miktar',
                        'is-approved'       => 'Onaylandı mı',
                        'yes'               => 'Evet',
                        'no'                => 'Hayır',
                        'delete'            => 'Sil',
                        'downloadable'      => 'İndirilebilir',
                        'virtual'           => 'Sanal',
                        'grouped_product'   => 'Gruplandırılmış Ürün',
                        'qty'               => 'Miktar',
                        'selelr_price'      => 'Satış Fiyatı',                       
                        'sort_order'        => 'Sıralama Sırası',
                        'bundle_product'    => 'Paket Ürün',
                        'option_title'      => 'Seçenek Başlığı',
                        'input_type'        => 'Giriş Türü',
                        'is_required'       => 'Zorunlu mu',
                        'is_default'        => 'Varsayılan mı',
                        'add_option'        => 'Seçenek Ekle',
                    ]
                ],

                'sales' => [
                    'orders' => [
                        'title'                  => 'Siparişler',
                        'view-title'             => 'Sipariş #:order_id',
                        'info'                   => 'Bilgiler',
                        'invoices'               => 'Faturalar',
                        'refunds'                => 'İadeler',
                        'shipments'              => 'Gönderimler',
                        'placed-on'              => 'Sipariş Tarihi',
                        'status'                 => 'Durum',
                        'customer-name'          => 'Müşteri Adı',
                        'email'                  => 'E-posta',
                        'inventory-source'       => 'Envanter Kaynağı',
                        'carrier-title'          => 'Taşıyıcı Başlığı',
                        'tracking-number'        => 'Takip Numarası',
                        'id'                     => 'Id',
                        'base-total'             => 'Temel Toplam',
                        'grand-total'            => 'Genel Toplam',
                        'order-date'             => 'Sipariş Tarihi',
                        'channel-name'           => 'Kanal Adı',
                        'processing'             => 'İşleniyor',
                        'completed'              => 'Tamamlandı',
                        'canceled'               => 'İptal Edildi',
                        'closed'                 => 'Kapandı',
                        'pending'                => 'Beklemede',
                        'pending-payment'        => 'Ödeme Bekliyor',
                        'fraud'                  => 'Dolandırıcılık',
                        'billed-to'              => 'Fatura Edilen',
                        'total-seller-amount'    => 'Toplam Satıcı Tutarı',
                        'total-admin-commission' => 'Toplam Admin Komisyonu',
                        'admin-commission'       => 'Admin Komisyonu',
                        'phone'                  => 'Telefon',
                        'gender'                 => 'Cinsiyet',
                        'address'                => 'Adres',
                        'order-count'            => 'Siparişler'
                    ],

                    'invoices' => [
                        'title'          => 'Faturalar',
                        'create-title'   => 'Fatura Oluştur',
                        'create'         => 'Oluştur',
                        'order-id'       => 'Sipariş Id',
                        'qty-ordered'    => 'Sipariş Edilen Miktar',
                        'qty-to-invoice' => 'Faturalanacak Miktar',
                        'product-name'   => 'Ürün Adı'
                    ],

                    'shipments' => [
                        'title'             => 'Gönderimler',
                        'create-title'      => 'Gönderim Oluştur',
                        'create'            => 'Oluştur',
                        'order-id'          => 'Sipariş Id',
                        'carrier-title'     => 'Taşıyıcı Başlığı',
                        'tracking-number'   => 'Takip Numarası',
                        'source'            => 'Kaynak',
                        'select-source'     => 'Lütfen Kaynak Seçin',
                        'product-name'      => 'Ürün Adı',
                        'qty-ordered'       => 'Sipariş Edilen Miktar',
                        'qty-to-ship'       => 'Gönderilecek Miktar',
                        'available-sources' => 'Mevcut Kaynaklar',
                        'qty-available'     => 'Mevcut Miktar'
                    ],

                    'transactions' => [
                        'title'            => 'İşlemler',
                        'view-title'       => 'İşlem #:transaction_id',
                        'id'               => 'Id',
                        'total'            => 'Toplam',
                        'transaction-id'   => 'İşlem Id',
                        'comment'          => 'Yorum',
                        'order-id'         => 'Sipariş #:order_id',
                        'commission'       => 'Komisyon',
                        'seller-total'     => 'Satıcı Toplamı',
                        'created-at'       => 'Oluşturma Tarihi',
                        'payment-method'   => 'Ödeme Yöntemi',
                        'total-sale'       => 'Toplam Satış',
                        'total-payout'     => 'Toplam Ödeme',
                        'remaining-payout' => 'Kalan Ödeme',
                        'sub-total'        => 'Ara Toplam',
                        'tax'              => 'Vergi',
                        'total-refunded'   => 'Toplam İade',
                    ],
    
                    'payment-request' => [
                        'request-payment' => 'Ödeme Talep Et',
                        'request-success' => 'Ödeme talebi gönderildi.'
                    ]
                ],
    
                'reviews' => [
                    'title'         => 'Yorumlar',
                    'id'            => 'Id',
                    'customer-name' => 'Müşteri adı',
                    'rating'        => 'Değerlendirme',
                    'comment'       => 'Yorum',
                    'status'        => 'Durum',
                    'approved'      => 'Onaylandı',
                    'pending'       => 'Beklemede',
                    'unapproved'    => 'Onaylanmadı',
                ]
            ],

            'profile' => [
                'count-products'  => ':count ürün',
                'contact-seller'  => 'Satıcıyla İletişim Kur',
                'total-rating'    => ':total_rating Değerlendirme & :total_reviews Yorum',
                'visit-store'     => 'Mağazayı Ziyaret Et',
                'about-seller'    => 'Mağaza Hakkında',
                'member-since'    => 'Üyelik Tarihi: :date',
                'all-reviews'     => 'Tüm Yorumlar',
                'return-policy'   => 'İade Politikası',
                'shipping-policy' => 'Kargo Politikası',
                'by-user-date'    => '- :name tarafından :date tarihinde',
                'name'            => 'Ad',
                'email'           => 'E-posta',
                'subject'         => 'Konu',
                'query'           => 'Sorgu',
                'submit'          => 'Gönder'
            ],

            'reviews' => [
                'title'         => 'Yorumlar - :shop_title',
                'create-title'  => 'Yorum Yaz - :shop_title',
                'write-review'  => 'Yorum Yaz',
                'total-rating'  => ':total_rating Değerlendirme & :total_reviews Yorum',
                'view-more'     => 'Daha Fazla Görüntüle',
                'by-user-date'  => '- :name tarafından :date tarihinde',
                'rating'        => 'Değerlendirme',
                'comment'       => 'Yorum'
            ],
    
            'products' => [
                'title' => 'Ürünler - :shop_title',
            ],
    
            'mails' => [
                'contact-seller' => [
                    'subject' => 'Satıcıya Karşı Rapor',
                    'dear'    => 'Sevgili :name',
                    'info'    => 'Umarım bu e-posta sizin için iyi geçerli. Karşılaştığım bir sorunu bildirmek için yazıyorum. Dikkatinizi aşağıdaki detaylara çekmek istiyorum:',
                    'issue'   => 'Karşılaşılan Sorun',
                    'thanks'  => 'Teşekkürler'
                ],
    
                'report-product' => [
                    'subject' => 'Ürün Bildirildi',
                    'dear'    => 'Sayın :name',
                    'info'    => ':name tarafından :reason sebebiyle ürününüz :product_name bildirildi',
                    'thanks'  => 'Teşekkürler'
                ],
    
                'report-product-toadmin' => [
                    'subject' => 'Satıcı Ürünü Bildirildi',
                    'dear'    => 'Sayın :name',
                    'info'    => ':name tarafından :reason sebebiyle satıcı ürünü :product_name bildirildi',
                    'thanks'  => 'Teşekkürler'
                ]
            ],
        ],

        'products' => [
            'popular-products'    => 'Popüler Ürünler',
            'all-products'        => 'Tüm Ürünler',
            'sold-by'             => 'Satıcı: :url',
            'seller-count'        => ':count Daha Fazla Satıcı',
            'more-sellers'        => 'Daha Fazla Satıcı',
            'seller-total-rating' => ':avg_rating (Ortalama puanlar)',
            'add-to-cart'         => 'Sepete Ekle',
            'new'                 => 'Yeni',
            'used'                => 'Kullanılmış',
            'out-of-stock'        => 'Stokta Yok'
        ],

        'flag' => [
            'title'           => 'Rapor Et',
            'name'            => 'Ad',
            'email'           => 'E-posta',
            'reason'          => 'Neden',
            'submit'          => 'Gönder',
            'error-msg'       => 'Bir şeyler yanlış gitti.',
            'success-msg'     => 'Ürün başarıyla rapor edildi.',
            'report-msg'      => 'Bu ürün için zaten rapor verdiniz',
            'self-report-err' => 'Kendinize rapor veremezsiniz',
            'seller-report'   => 'Satıcı başarıyla rapor edildi.'
        ],

        'review' => [
            'success-msg' => 'Satıcı yorumu başarıyla gönderildi.',
            'self-review' => 'Kendinizi değerlendiremezsiniz'
        ],
    ],
    
    'admin' => [
        'layouts' => [
            'marketplace'       => 'Pazar Yeri',
            'sellers'           => 'Satıcılar',
            'products'          => 'Ürünler',
            'product-reviews'   => 'Ürün İncelemeleri',
            'seller-reviews'    => 'Satıcı Yorumları',
            'orders'            => 'Siparişler',
            'transactions'      => 'İşlemler',
            'payment-requests'  => 'Ödeme Talepleri'
        ],
    
        'dashboard' => [
            'remaining-payout'        => 'Kalan Ödeme',
            'sellers-with-most-sales' => 'En Çok Satış Yapan Üst Satıcılar'
        ],
    
        'acl' => [
            'marketplace' => 'Pazar Yeri',
            'sellers'     => 'Satıcılar',
            'products'    => 'Ürünler',
            'reviews'     => 'Satıcı Yorumları'
        ],
    
        'system' => [
            'marketplace'                       => 'Pazar Yeri',
            'module-information'                => 'Modül Bilgileri',
            'settings'                          => 'Ayarlar',
            'general'                           => 'Genel',
            'commission-per-unit'               => 'Birim Başına Komisyon (Yüzde)',
            'seller-approval-required'          => 'Satıcı Onayı Gerekli',
            'product-approval-required'         => 'Ürün Onayı Gerekli',
            'can-create-invoice'                => 'Satıcı Fatura Oluşturabilir',
            'can-create-shipment'               => 'Satıcı Sevkiyat Oluşturabilir',
            'can_cancel_order'                  => 'Satıcı Siparişi İptal Edebilir',
            'yes'                               => 'Evet',
            'no'                                => 'Hayır',
            'landing-page'                      => 'Varsayılan İçerik',
            'page-title'                        => 'Sayfa Başlığı',
            'show-banner'                       => 'Banner Göster',
            'layout'                            => 'Sayfa Düzeni',
            'banner'                            => 'Banner',
            'banner-content'                    => 'Banner İçeriği',
            'show-features'                     => 'Özellikleri Göster',
            'feature-heading'                   => 'Özellik Başlığı',
            'feature-info'                      => 'Özellik Bilgisi',
            'feature-icon-1'                    => 'Özellik İkonu 1',
            'feature-icon-label-1'              => 'Özellik İkonu Etiketi 1',
            'feature-icon-2'                    => 'Özellik İkonu 2',
            'feature-icon-label-2'              => 'Özellik İkonu Etiketi 2',
            'feature-icon-3'                    => 'Özellik İkonu 3',
            'feature-icon-label-3'              => 'Özellik İkonu Etiketi 3',
            'feature-icon-4'                    => 'Özellik İkonu 4',
            'feature-icon-label-4'              => 'Özellik İkonu Etiketi 4',
            'feature-icon-5'                    => 'Özellik İkonu 5',
            'feature-icon-label-5'              => 'Özellik İkonu Etiketi 5',
            'feature-icon-6'                    => 'Özellik İkonu 6',
            'feature-icon-label-6'              => 'Özellik İkonu Etiketi 6',
            'feature-icon-7'                    => 'Özellik İkonu 7',
            'feature-icon-label-7'              => 'Özellik İkonu Etiketi 7',
            'feature-icon-8'                    => 'Özellik İkonu 8',
            'feature-icon-label-8'              => 'Özellik İkonu Etiketi 8',
            'show-popular-sellers'              => 'Popüler Satıcıları Göster',
            'open-shop-button-label'            => 'Dükkan Açma Düğme Etiketi',
            'about-marketplace'                 => 'Pazar Yeri Hakkında',
            'show-open-shop-block'              => 'Dükkan Açma Bloğunu Göster',
            'open-shop-info'                    => 'Dükkan Açma Bilgisi',
            'setup-icon-1'                      => 'Kurulum İkonu 1',
            'setup-icon-2'                      => 'Kurulum İkonu 2',
            'setup-icon-3'                      => 'Kurulum İkonu 3',
            'setup-icon-4'                      => 'Kurulum İkonu 4',
            'setup-icon-5'                      => 'Kurulum İkonu 5',
            'seller-flag'                       => 'Satıcı Bayrakları',
            'product-flag'                      => 'Ürün Bayrakları',
            'enable'                            => 'Etkinleştir',
            'text'                              => 'Metin',
            'guest-can'                         => 'Misafir Bayrak Gönderebilir',
            'reason'                            => 'Nedenler',
            'other-reason'                      => 'Diğer Nedenleri Kabul Et',
            'other-placeholder'                 => 'Diğer Alanın Yer Tutucusu',
            'minimum-order-amount'              => 'Minimum Sipariş Tutarı Ayarları',
            'min-amount'                        => 'Minimum Tutar',
            'seller-min-amount'                 => 'Satıcı için tutar değeri',
            'google-analytics-id'               => 'Google Analytics Kimliği',
            'google-analytics'                  => 'Google Analytics',
            'seller-google-analytics'           => 'Satıcı Google Analytics etkinleştirin',
            'status'                            => 'Durum',
            'featured'                          => 'Satıcı için Öne Çıkan Ürün listesine izin ver.',
            'new'                               => 'Satıcı için Yeni Ürün listesine izin ver.',
            'products-setting'                  => 'Ürün Oluşturmayı Yönet',
            'marketplace-product-type'          => 'Pazar Yeri Ürün Türü',
            'allow_marketplace_booking_product' => 'Pazar Yeri Rezervasyonlu Ürüne İzin Ver',
            'allow_marketplace_bundle_product'  => 'Pazar Yeri Paket Ürüne İzin Ver',
            'allow_marketplace_grouped_product' => 'Pazar Yeri Gruplu Ürüne İzin Ver'
        ],
    
        'flag' => [
            'title'  => 'Bayraklar',
            'name'   => 'İsim',
            'email'  => 'E-posta',
            'reason' => 'Nedenler'
        ],
    
        'sellers' => [
            'title'                 => 'Satıcılar',
            'owner'                 => 'Mal sahibi',
            'create'                => 'Satıcı Oluştur',
            'add-title'             => 'Satıcı',
            'save-btn-title'        => 'Satıcıyı Kaydet',
            'id'                    => 'Kimlik',
            'seller-name'           => 'Satıcı Adı',
            'seller-email'          => 'Satıcı E-postası',
            'customer-name'         => 'Müşteri Adı',
            'customer-email'        => 'Müşteri E-postası',
            'created-at'            => 'Oluşturulma Tarihi',
            'is-approved'           => 'Onaylı mı',
            'approved'              => 'Onaylı',
            'un-approved'           => 'Onaylanmamış',
            'approve'               => 'Onayla',
            'unapprove'             => 'Onayı Kaldır',
            'delete'                => 'Sil',
            'update'                => 'Güncelle',
            'delete-success-msg'    => 'Satıcı başarıyla silindi.',
            'mass-delete-success'   => 'Seçilen satıcılar başarıyla silindi.',
            'mass-update-success'   => 'Seçilen satıcılar başarıyla güncellendi.',
            'mass-update-disable'   => 'Seçilen satıcılar devre dışı bırakıldı, öğeleri atayamazsınız',
            'update-success'        => 'Satıcı başarıyla güncellendi.',
            'product'               => 'Ürün',
            'add-product'           => 'Ürün Ekle',
            'search'                => 'Ara',
            'search-product'        => 'Ürün Ara',
            'assign-product'        => 'Ürünü Satıcıya Ata',
            'commission'            => 'Komisyon',
            'change-commission'     => 'Komisyonu Değiştir',
            'commission-percentage' => 'Komisyon Yüzdesi',
            'seller-profile'        => 'Satıcı Profili',
            'view-seller-profile'   => 'Satıcı Profilini Görüntüle',

            'flag' => [
                'title'          => 'Satıcı Bayrak Nedenleri',
                'add-btn-title'  => 'Bayrak Nedeni Ekle',
                'create-success' => 'Satıcı bayrak nedeni başarıyla oluşturuldu',
                'update-success' => 'Satıcı bayrak nedeni başarıyla güncellendi',
                'delete-success' => 'Satıcı bayrak nedeni başarıyla silindi',
                'edit-title'     => 'Bayrak nedenini düzenle',
    
                'create' => [
                    'add-title'        => 'Neden Oluştur',
                    'create-btn-title' => 'Kaydet',
                    'reason'           => 'İsim',
                    'status'           => 'Durum'
                ]
            ],
    
            'category' => [
                'title'             => 'Satıcı Kategorisi',
                'add-title'         => 'Satıcıya Kategori Ata',
                'add-btn-title'     => 'Kategori Ata',
                'save-btn-title'    => 'Atanan Kategoriyi Kaydet',
                'edit-title'        => 'Satıcıya Atanan Kategoriyi Güncelle',
                'edit-btn-title'    => 'Atanan Kategoriyi Güncelle',
                'update-btn-title'  => 'Atanan Kategoriyi Güncelle',
                'create'            => 'Kategori Ata',
                'seller'            => 'Satıcı Seçin',
                'update-success'    => 'Atanan kategori başarıyla güncellendi',
                'delete-success'    => 'Atanan kategori başarıyla silindi',
                'save-success'      => 'Kategoriler başarıyla atanmıştır',
                'delete-failed'     => 'Silinemedi',
                'save-error'        => 'Lütfen en az bir kategori seçin',
            ]
        ],

        'orders' => [
            'title'                 => 'Siparişler',
            'manage-title'          => 'Satıcının Siparişlerini Yönet',
            'order-id'              => 'Sipariş ID',
            'seller-name'           => 'Satıcı Adı',
            'sub-total'             => 'Alt Toplam',
            'grand-total'           => 'Genel Toplam',
            'commission'            => 'Komisyon',
            'discount'              => 'İndirim',
            'seller-total'          => 'Satıcı Toplamı',
            'total-paid'            => 'Ödenen Toplam',
            'remaining-total'       => 'Kalan Toplam',
            'invoice-pending'       => 'Fatura Bekliyor',
            'order-canceled'        => 'Sipariş İptal Edildi',
            'seller-total-invoiced' => 'Faturalandırıldı',
            'order-date'            => 'Sipariş Tarihi',
            'channel-name'          => 'Kanal Adı',
            'status'                => 'Durum',
            'processing'            => 'İşleniyor',
            'completed'             => 'Tamamlandı',
            'canceled'              => 'İptal Edildi',
            'closed'                => 'Kapandı',
            'pending'               => 'Beklemede',
            'pending-payment'       => 'Ödeme Bekliyor',
            'fraud'                 => 'Dolandırıcılık',
            'billed-to'             => 'Fatura Edilen',
            'withdrawal-requested'  => 'Çekilme İsteği',
            'pay'                   => 'Öde',
            'already-paid'          => 'Zaten Ödendi',
            'yes'                   => 'Evet',
            'no'                    => 'Hayır',
            'pay-seller'            => 'Satıcıya Öde',
            'comment'               => 'Yorum',
            'payment-success-msg'   => 'Bu satıcı için ödeme başarıyla gerçekleştirildi',
            'order-not-exist'       => 'Sipariş mevcut değil',
            'no-amount-to-paid'     => 'Bu satıcıya ödenmesi gereken miktar kalmadı.',
            'refunded'              => 'İade Edildi',
            'requested'             => 'İstendi',
        ],
    
        'transactions' => [
            'title'            => 'İşlemler',
            'id'               => 'ID',
            'seller-name'      => 'Satıcı Adı',
            'total'            => 'Toplam',
            'transaction-id'   => 'İşlem ID',
            'comment'          => 'Yorum',
            'order-id'         => 'Sipariş #:order_id',
            'commission'       => 'Komisyon',
            'seller-total'     => 'Satıcı Toplamı',
            'created-at'       => 'Oluşturulma Tarihi',
            'payment-method'   => 'Ödeme Yöntemi',
            'total-sale'       => 'Toplam Satış',
            'total-payout'     => 'Toplam Ödeme',         
            'remaining-payout' => 'Kalan Ödeme',
            'seller-id'        => 'Satıcı ID',
        ],

        'payment-request' => [
            'title' => 'Ödeme Talepleri'
        ],
    
        'products' => [
            'id'                      => 'ID',
            'title'                   => 'Ürünler',
            'product-id'              => 'Ürün ID',
            'seller-name'             => 'Ad',
            'product-number'          => 'Ürün Numarası',
            'sku'                     => 'Sku',
            'name'                    => 'Ad',
            'description'             => 'Açıklama',
            'url-key'                 => 'URL Anahtarı',
            'price'                   => 'Fiyat',
            'cost'                    => 'Maliyet',
            'weight'                  => 'Ağırlık',
            'color'                   => 'Renk',
            'size'                    => 'Beden',
            'quantity'                => 'Miktar',
            'status'                  => 'Durum',
            'is-approved'             => 'Onaylandı mı',
            'approved'                => 'Onaylandı',
            'un-approved'             => 'Onaylanmadı',
            'approve'                 => 'Onayla',
            'unapprove'               => 'Onayı Kaldır',
            'delete'                  => 'Sil',
            'update'                  => 'Güncelle',
            'configurable-attributes' => 'Yapılandırılabilir Özellikler',
            'general'                 => 'Genel',
            'delete-success-msg'      => 'Ürün başarıyla silindi.',
            'mass-delete-success'     => 'Seçilen ürünler başarıyla silindi.',
            'mass-update-success'     => 'Seçilen ürünler başarıyla güncellendi.',
            'mass-update-disable'     => 'Onaylanamaz, Satıcı devre dışı',
    
            'flag' => [
                'flag-title'     => 'Bayraklar',
                'title'          => 'Ürün Bayrak Nedenleri',
                'reason'         => 'Neden',
                'status'         => 'Durum',
                'create-success' => 'Ürün bayrağı başarıyla oluşturuldu',
                'delete-success' => 'Ürün bayrağı başarıyla silindi',
                'update-success' => 'Ürün bayrağı başarıyla güncellendi'
            ]
        ],
    
        'reviews' => [
            'title'               => 'Yorumlar',
            'id'                  => 'ID',
            'comment'             => 'Yorum',
            'rating'              => 'Oylama',
            'customer-name'       => 'Müşteri Adı',
            'seller-name'         => 'Satıcı Adı',
            'status'              => 'Durum',
            'approved'            => 'Onaylandı',
            'un-approved'         => 'Onaylanmadı',
            'approve'             => 'Onayla',
            'unapprove'           => 'Onayı Kaldır',
            'update'              => 'Güncelle',
            'mass-update-success' => 'Seçilen yorumlar başarıyla güncellendi.'
        ],

        'response' => [
            'create-success' => ':name başarıyla oluşturuldu.',
            'update-success' => ':name başarıyla güncellendi.',
            'delete-success' => ':name başarıyla silindi.',
        ]
    ],

    'mail' => [
        'seller' => [
            'welcome' => [
                'subject' => 'Satıcı Talebi Bildirimi',
                'dear'    => 'Sevgili :name',
                'info'    => 'Satıcı olarak kaydolduğunuz için teşekkür ederiz, hesabınız incelenmektedir. Hesap onayınız hakkında size e-posta yoluyla bilgilendirme yapacağız.'
            ],
    
            'approval' => [
                'subject'           => 'Satıcı Onay Bildirimi',
                'dear'              => 'Sevgili :name',
                'info'              => 'Size satıcı olarak onay verildiğini bildirmek için bu e-postayı gönderiyoruz. Giriş yapmak için aşağıdaki düğmeyi tıklayın.',
                'login'             => 'Giriş Yap',
                'disapprove-seller' => 'Satıcı Onayı Kaldırma Bildirimi',
                'disapprove-info'   => 'Size satıcı olarak onay verilmediğini bildirmek için bu e-postayı gönderiyoruz. Giriş yapmak için aşağıdaki düğmeyi tıklayın.'
            ],
    
            'update' => [
                'subject' => 'Satıcı Güncelleme Bildirimi',
                'dear'    => 'Sevgili :name',
                'info'    => 'Bilgilerinizin satıcı olarak güncellendiğini bildirmek için bu e-postayı gönderiyoruz. Giriş yapmak için aşağıdaki düğmeyi tıklayın.',
                'login'   => 'Giriş Yap',
            ],
    
            'regisration' =>  [
                'subject' => 'Yeni Satıcı Bildirimi',
                'dear'    => 'Merhaba :name',
                'info'    => 'Pazaryerinizde yeni bir satıcı :name kaydoldu. Lütfen yönetim panelinizden onaylayın.',
            ],
    
            'good-bye' => [
                'subject' => 'Satıcı Silme Bildirimi',
                'dear'    => 'Sevgili :name',
                'info'    => 'Satıcı olarak kaydolduğunuz için teşekkür ederiz, hesabınız başarıyla silinmiştir.'
            ],
        ],
    
        'sales' => [
            'order' => [
                'subject'  => 'Yeni Sipariş Bildirimi',
                'greeting' => 'Yeni bir Siparişiniz :order_id, :created_at tarihinde alındı.',
            ],
    
            'invoice' => [
                'subject'  => 'Yeni Fatura Bildirimi',
                'greeting' => 'Yeni bir Faturanız var, :order_id için :created_at tarihinde oluşturuldu.',
            ],

            'shipment' => [
                'heading'  => 'Gönderi Onayı!',
                'subject'  => 'Yeni Gönderi Bildirimi',
                'greeting' => 'Sipariş :order_id için yeni bir gönderiniz var, :created_at tarihinde oluşturuldu.',
            ],

            'paymentRequest' => [
                'subject'     => 'Ödeme Talebi Bildirimi',
                'dear'        => 'Merhaba :name',
                'heading'     => 'Ödeme Talebi Bildirimi',
                'request'     => 'Bir satıcı :seller_name, siparişi için ödeme talebinde bulundu. Sipariş detayları aşağıdaki gibidir',
                'requestdone' => 'Siparişiniz için ödeme talebiniz tamamlandı. Sipariş detayları aşağıdaki gibidir'
            ]
        ],
    
        'product' => [
                'subject'            => 'Ürün Onay Bildirimi',
                'disapprove-product' => 'Ürün Onayı Kaldırma Bildirimi',
                'dear'               => 'Sevgili :name',
                'info'               => 'Ürününüz <b>:name</b> onaylandığını bildirmek için bu e-postayı gönderiyoruz.',
                'disapproved-info'   => 'Ürününüz <b>:name</b> onayı kaldırıldığını bildirmek için bu e-postayı gönderiyoruz.'
        ],
    
        'flag' => [
            'title' => ''
        ],
    
        'payment' => [
            'dear'    => 'Sevgili :customer_name',
            'request' => 'Bir satıcı :seller_name, siparişi için ödeme talebinde bulundu. Sipariş detayları aşağıdaki gibidir'
        ]
    ],
    
    'velocity' => [
        'system' => [
            'velocity-content' => 'Hız İçeriği'
        ]
    ]
];
