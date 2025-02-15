<?php

namespace Kho8k\ThemeVung;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class ThemeVungServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->setupDefaultThemeCustomizer();
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'themes');

        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('themes/vung')
        ], 'vung-assets');
    }

    protected function setupDefaultThemeCustomizer()
    {
        config(['themes' => array_merge(config('themes', []), [
            'vung' => [
                'name' => 'Vung',
                'author' => 'opdlnf01@gmail.com',
                'package_name' => 'kho8k/theme-vung',
                'publishes' => ['vung-assets'],
                'preview_image' => '',
                'options' => [
                    [
                        'name' => 'recommendations_limit',
                        'label' => 'Recommended movies limit',
                        'type' => 'number',
                        'value' => 10,
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-4',
                        ],
                        'tab' => 'List'
                    ],
                    [
                        'name' => 'per_page_limit',
                        'label' => 'Pages limit',
                        'type' => 'number',
                        'value' => 36,
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-4',
                        ],
                        'tab' => 'List'
                    ],
                    [
                        'name' => 'movie_related_limit',
                        'label' => 'Movies related limit',
                        'type' => 'number',
                        'value' => 12,
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-4',
                        ],
                        'tab' => 'List'
                    ],
                    [
                        'name' => 'latest',
                        'label' => 'Home Page',
                        'type' => 'code',
                        'hint' => 'display_label|relation|find_by_field|value|limit|show_more_url',
                        'value' => <<<EOT
                        Phim chiếu rạp||is_shown_in_theater|1|updated_at|desc|12|/danh-sach/phim-chieu-rap
                        Phim bộ mới||type|series|updated_at|desc|12|/danh-sach/phim-bo
                        Phim lẻ mới||type|single|updated_at|desc|12|/danh-sach/phim-le
                        Hoạt hình|categories|slug|hoat-hinh|updated_at|desc|12|/the-loai/hoat-hinh
                        EOT,
                        'attributes' => [
                            'rows' => 5
                        ],
                        'tab' => 'List'
                    ],
                    [
                        'name' => 'hotest',
                        'label' => 'Danh sách hot',
                        'type' => 'code',
                        'hint' => 'Label|relation|find_by_field|value|sort_by_field|sort_algo|limit|show_template (top_thumb|top_poster|top_poster_small)',
                        'value' => <<<EOT
                        Phim sắp chiếu||type|single|view_week|desc|10|top_poster_small
                        Top phim lẻ||type|single|view_week|desc|5|top_thumb
                        Top phim bộ||type|series|view_week|desc|10|top_poster
                        EOT,
                        'attributes' => [
                            'rows' => 5
                        ],
                        'tab' => 'List'
                    ],
                    [
                        'name' => 'additional_css',
                        'label' => 'Additional CSS',
                        'type' => 'code',
                        'value' => "<style>img.logoiframe {width: 15%;position: absolute;top: 2%;left: 3%;background-color: #00000010;z-index: 100;} #player-holder{position: relative;}</style>",
                        'tab' => 'Custom CSS'
                    ],
                    [
                        'name' => 'body_attributes',
                        'label' => 'Body attributes',
                        'type' => 'text',
                        'value' => "class='body-page '",
                        'tab' => 'Custom CSS'
                    ],
                    [
                        'name' => 'additional_header_js',
                        'label' => 'Header JS',
                        'type' => 'code',
                        'value' => "",
                        'tab' => 'Custom JS'
                    ],
                    [
                        'name' => 'additional_body_js',
                        'label' => 'Body JS',
                        'type' => 'code',
                        'value' => <<<HTML
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                setTimeout(function() {
                                    var playerDiv = document.getElementById("player-holder");

                                    if (playerDiv) {
                                        var imgElement = document.createElement("img");
                                        imgElement.src = "/storage/images/logovl.png";  // Đường dẫn hình ảnh
                                        imgElement.alt = "logo";  // Thuộc tính alt của ảnh
                                        imgElement.className = "logoiframe";  // Thêm class 'logoiframe'
                                        playerDiv.appendChild(imgElement);
                                    }
                                }, 500); // Chờ 1 giây sau khi script trước đã thực thi
                            });
                        </script>
                        <script>
                        var catfishDiv = `<div class="custom-banner-video">
                                                <div class="banner-ads">
                                                </div>
                                            </div>
                                            <style>
                                            .custom-banner-video {
                                                text-align: center;
                                                margin: 5px;
                                            }
                                            .custom-banner-video img {
                                                max-width: 100%;
                                            }
                                            </style>
                                            `;
                                            var headerDiv = `
                                            <div class="custom-banner-video">
                                                <div class="banner-ads">
                                                </div>
                                            </div>
                                            <style>
                                            .custom-banner-video {
                                                text-align: center;
                                                margin: 5px;
                                            }
                                            .custom-banner-video img {
                                                max-width: 100%;
                                            }
                                            </style>`;

                        var targetBottomElement = document.querySelector("#player-wrapper");
                        var targetTopElement = document.querySelector("#player-wrapper");
                        if (targetBottomElement) {
                            targetBottomElement.insertAdjacentHTML("afterend", catfishDiv);
                        }
                        if (targetTopElement) {
                            targetTopElement.insertAdjacentHTML("afterbegin", headerDiv);
                        }
                        </script>
                        HTML,
                        'tab' => 'Custom JS'
                    ],
                    [
                        'name' => 'additional_footer_js',
                        'label' => 'Footer JS',
                        'type' => 'code',
                        'value' => "",
                        'tab' => 'Custom JS'
                    ],
                    [
                        'name' => 'footer',
                        'label' => 'Footer',
                        'type' => 'code',
                        'value' => <<<EOT
                        <footer>
                            <div class="footer1">
                                <a href="/" style="background-image:url(https://Kho8k1.cc/logo-Kho8k-5.png)"></a>
                                <ul>
                                <li>
                                    <a href="#">Hỏi đáp - Hướng dẫn</a>
                                </li>
                                <li>
                                    <a href="#">Điều khoản sử dụng</a>
                                </li>
                                <li>
                                    <a href="#">Chính sách riêng tư</a>
                                </li>
                                <li>
                                    <a href="#">Nguyên tắc Cộng Đồng</a>
                                </li>
                                <li>
                                    <a href="#">Liên hệ Quảng Cáo</a>
                                </li>
                                </ul>
                                <div>Copyright ©2022 kho8k.</div>
                            </div>
                        </footer>
                        EOT,
                        'tab' => 'Custom HTML'
                    ],
                    [
                        'name' => 'ads_header',
                        'label' => 'Ads header',
                        'type' => 'code',
                        'value' => '',
                        'tab' => 'Ads'
                    ],
                    [
                        'name' => 'ads_catfish',
                        'label' => 'Ads catfish',
                        'type' => 'code',
                        'value' => '',
                        'tab' => 'Ads'
                    ]
                ],
            ]
        ])]);
    }
}
