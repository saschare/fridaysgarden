<!DOCTYPE html>
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="de"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="de"> <!--<![endif]-->
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <script type="application/x-moraso" src="HTML.Meta.PageTitle">
            suffix = " | friday's garden"
        </script>

        <!-- BaseHref :: Start -->
        <script type="application/x-moraso" src="HTML.Meta.BaseHref"></script>
        <!-- BaseHref :: End -->

        <!-- CanonicalTag :: Start -->
        <script type="application/x-moraso" src="HTML.Meta.CanonicalTag"></script>
        <!-- CanonicalTag :: End -->

        <!-- Meta Tags :: Start -->
        <script type="application/x-moraso" src="HTML.Meta.Tags"></script>
        <!-- Meta Tags :: End -->

        <!-- Meta Geo Tags :: Start -->
        <meta name="geo.region" content="DE-NI" />
        <meta name="geo.placename" content="Hoya" />
        <meta name="geo.position" content="52.80912;9.14234" />
        <meta name="ICBM" content="52.80912, 9.14234" />
        <!-- Meta Geo Tags :: End -->

        <!-- Windows 8 Tile :: Start -->
        <meta name="application-name" content="moraso CMS" /> 
        <!--<meta name="msapplication-TileImage" content="<?php echo Aitsu_Config::get('sys.webpath'); ?>skin/gfx/tileImage.png" />-->
        <meta name="msapplication-TileColor" content="#FFFFFF" />
        <!-- Windows 8 Tile :: End -->

        <!-- CSS :: Start -->
        <link rel="stylesheet" href="/skin/css/foundation.min.css">
        <link rel="stylesheet" href="/skin/css/app.css">
        <!-- CSS :: End -->

        <!-- JS :: Start -->
        <script src="/skin/js/vendor/prefixfree.min.js"></script>
        <script src="/skin/js/vendor/custom.modernizr.js"></script>
        <!-- JS :: End -->

        <!-- Google Fonts :: Start -->
        <link href='http://fonts.googleapis.com/css?family=Raleway:400,700' rel='stylesheet' type='text/css'>
        <!-- Google Fonts :: End -->

        <!-- IE Fix for HTML5 Tags -->
        <!--[if lt IE 9]>
                <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>


        <!-- Header :: Start -->
        <header id="header">
            <div class="row">
                <div class="large-12 columns">
                    <script type="application/x-moraso" src="Template:Header">
                        defaultTemplate = default

                        template.default.name = Standard
                        template.default.file = templates/header/default.phtml 
                    </script>
                </div>
            </div>
        </header>
        <!-- Header :: End -->

        <!-- Content :: Start -->
        <div class="row" role="main">
            <div id="page-wrapper" class="large-12 columns">
                <script type="application/x-moraso" src="Template:Main">
                    defaultTemplate = default

                    template.default.name = Standard
                    template.default.file = templates/main/default.phtml 

                    template.start.name = Start
                    template.start.file = templates/main/start.phtml 
                </script>
            </div>
        </div>
        <!-- Content :: End -->

        <!-- Footer :: Start -->
        <footer id="footer">
            <div class="row">
                <div class="large-12 columns">
                    <script type="application/x-moraso" src="Template:Footer">
                        defaultTemplate = default

                        template.default.name = Standard
                        template.default.file = templates/footer/default.phtml 
                    </script>
                </div>
            </div>
        </footer>
        <!-- Footer :: End -->

        <!-- JS :: Start -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="/skin/js/foundation.min.js"></script>
        <script>
            $(document).foundation();
        </script>
        <!-- JS :: End -->
    </body>
</html>
