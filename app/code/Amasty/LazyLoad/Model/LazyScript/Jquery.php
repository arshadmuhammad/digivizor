<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\LazyScript;

class Jquery implements LazyScriptInterface
{
    public function getName(): string
    {
        return (string)__('jQuery Lazy Script');
    }

    public function getType(): string
    {
        return 'jquery';
    }

    public function getCode(): string
    {
        return '<script>
                window.amlazycallback = function () {
                    window.jQuery("img[data-amsrc]").lazy({
                        "bind":"event",
                        "attribute": "data-amsrc",
                        "visibleOnly": true
                    });
                };
                require(["jquery"], function (jquery) {
                    require(["Amasty_LazyLoad/js/jquery.lazy"], function(lazy) {
                        if (document.readyState === "complete") {
                            window.jQuery("img[data-amsrc]").lazy({
                                "bind":"event",
                                "attribute": "data-amsrc",
                                "visibleOnly": true
                            });
                        } else {
                            window.jQuery("img[data-amsrc]").lazy({
                                "attribute": "data-amsrc",
                                "visibleOnly": true
                            });
                        }
                    })
                });
            </script>';
    }
}
