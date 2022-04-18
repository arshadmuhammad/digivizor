<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\LazyScript;

class Vanilla implements LazyScriptInterface
{
    public function getName(): string
    {
        return (string)__('Vanilla Lazy Script');
    }

    public function getType(): string
    {
        return 'vanilla';
    }

    public function getCode(): string
    {
        return '<script>
                window.amlazycallback = function () {
                    if (typeof window.amLazyLoadInstance !== "undefined") {
                        window.amLazyLoadInstance.update();
                    }
                };
                window.lazyLoadOptions = {
                    "data_src": "amsrc",
                    "elements_selector": "img[data-amsrc]"
                };
                window.addEventListener(
                    "LazyLoad::Initialized",
                    function (event) {
                        window.amLazyLoadInstance = event.detail.instance;
                    },
                    false
                );
                function amLazyLoadVanillaLib() {
                    var dependencies = ["Amasty_LazyLoad/js/vanilla.lazyload"];
                    // Dynamically define the dependencies
                    if (!("IntersectionObserver" in window)) {
                        dependencies.unshift("Amasty_LazyLoad/js/intersection-observer");
                    }

                    require(dependencies, function() {});
                }
                amLazyLoadVanillaLib();
            </script>';
    }
}
