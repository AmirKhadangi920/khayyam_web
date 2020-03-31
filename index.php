<?php

require("./vendor/autoload.php");
require('./File.php');

$file = new File();
$exercises = $file->get_exercises();

if ($_GET['exercise'] ?? false)
    $homeworks = $file->get_homeworks($_GET['exercise']);

if (isset($_GET['exercise']) && isset($_GET['homework'])) {
    $homework = $file->get_homework($_GET['exercise'], $_GET['homework']);
    $document = file_get_contents($homework);

    $validator = new HtmlValidator\Validator();
    $result = $validator->validateDocument($document);

    $res = [];

    if (file_exists('result.json')) {
        $myfile = fopen("result.json", "r");
        $res = json_decode(fread($myfile, filesize("result.json")), true);
        fclose($myfile);
    }

    if (isset($_GET['point'])) {

        $file = fopen("result.json", "w");
        $res[$_GET['homework']] = (int) $_GET['point'];
        fwrite($file, json_encode($res, JSON_PRETTY_PRINT));
        fclose($file);
    }

    if (isset($res[$_GET['homework']]))
        $point = $res[$_GET['homework']];
}

?>

<!DOCTYPE html>
<html lang="en" class="w-100 h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بررسی تمرین ها</title>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.18.1/styles/atom-one-dark-reasonable.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        /* for block of numbers */
        .hljs-ln-numbers {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;

            text-align: center;
            color: #ccc;
            border-right: 1px solid #CCC;
            vertical-align: top;
            padding-right: 5px;

            /* your custom style here */
        }

        /* for block of code */
        .hljs-ln-code {
            padding-left: 10px;
        }

        .hljs-ln td:first-of-type {
            padding-right: 10px !important;
        }

        .hljs-ln td:last-of-type {
            padding-left: 10px !important;
        }

        #error {
            background-color: #616773;
        }

        #error strong {
            color: #dc3545 !important;
        }

        #error .highlight {
            color: #ffc107 !important;
        }
    </style>
</head>

<body class="w-100 h-100">
    <div class="container-fluid h-100 p-0">
        <div class="row w-100 h-100 m-0 overflow-hidden">
            <?php if (isset($homework)) { ?>
                <div class="col-5 p-0 h-100 overflow-auto">
                    <pre class="m-0"><code class="w-100 p-2 h-100 html"><?= htmlspecialchars(file_get_contents($homework)) ?></code></pre>
                </div>
                <div class="col-5 p-0 h-100 overflow-auto">
                    <div class="<?= count($result->getErrors()) != 0 ? 'h-75' : 'h-100' ?> overflow-auto">
                        <iframe class="w-100 h-100" src="<?= $homework ?>"></iframe>
                    </div>
                    <?php if (count($result->getErrors()) != 0) { ?>
                        <div id="error" class="h-25 overflow-auto p-4 text-white"><?= $result->toHTML() ?></div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="col-10 d-flex align-items-center justify-content-center bg-primary text-white">
                    <h5 class="bg-dark rounded py-3 px-4">ابتدا یکی از تمرین های ارسال شده را انتخاب بکنید</h5>
                </div>
            <?php } ?>

            <div class="col-2 p-3 h-100 overflow-auto text-right" dir="rtl">
                <h5 class="mb-3">تمرین ها</h5>
                <div class=" list-group">
                    <?php foreach ($exercises as $item) { ?>
                        <a href="/?exercise=<?= $item ?>" class="list-group-item list-group-item-action <?php if (isset($_GET['exercise']) && $item == $_GET['exercise']) echo 'active'; ?>"><?= $item ?></a>
                    <?php } ?>
                </div>


                <?php if (isset($homeworks) && $homeworks) { ?>
                    <hr class="mt-5 mb-4" />

                    <h5 class="mb-3">ارسال ها</h5>
                    <div class=" list-group">
                        <?php foreach ($homeworks as $item) { ?>
                            <a href="/?exercise=<?= $_GET['exercise'] ?>&homework=<?= $item ?>" class="list-group-item list-group-item-action <?php if (isset($_GET['homework']) && $item == $_GET['homework']) echo 'active'; ?>"><?= $item ?></a>
                        <?php } ?>
                    </div>

                    <?php if (isset($homework)) { ?>
                        <hr class="mt-5 mb-4" />
                        <h5 class="mb-3">ثبت امتیاز</h5>
                        <form action="/" method="GET">
                            <input type="hidden" name="exercise" value="<?= $_GET['exercise'] ?>">
                            <input type="hidden" name="homework" value="<?= $_GET['homework'] ?>">

                            <div class="input-group flex-nowrap mb-2" dir="ltr">
                                <div class="input-group-prepend">
                                    <button class="btn btn-success" type="submit">ثبت</button>
                                </div>
                                <input dir="rtl" value="<?= $point ?? null ?>" type="number" min="0" max="100" name="point" class="form-control" placeholder="امتیاز" aria-describedby="addon-wrapping">
                            </div>
                            <span class="text-muted" style="font-size: 12px">لطفا بعد از بررسی یک نمره بین 0 تا 100 ثبت کنید</span>
                        </form>
                    <?php } ?>

                <?php } else { ?>
                    <div class="alert alert-primary mt-5" role="alert">
                        جهت مشاهده لیست تمرین های ارسالی ، لطفا یکی از تمرین های لیست بالا را انتخاب بکنید
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
    <!-- <a href="<?= $homework ?>" target="_blank">بازکردن</a> -->

    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.18.1/highlight.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlightjs-line-numbers.js/2.7.0/highlightjs-line-numbers.min.js"></script>

    <script>
        hljs.initHighlightingOnLoad();
        hljs.initLineNumbersOnLoad();
    </script>
</body>

</html>