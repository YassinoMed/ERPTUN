<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('PAYTR Plan Payment') }}</title>
    <style>
        body {
            margin: 0;
            font-family: Poppins, sans-serif;
            background: radial-gradient(circle at top, #f3f0e7 0%, #eef3f8 45%, #f8fafc 100%);
            color: #142235;
        }
        .pay-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 18px;
        }
        .pay-card {
            width: 100%;
            max-width: 960px;
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(20,34,53,0.08);
            border-radius: 28px;
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }
        .pay-hero {
            padding: 28px 30px 20px;
            background: linear-gradient(135deg, #132238 0%, #1f4b6e 100%);
            color: #fff;
        }
        .pay-hero h1 {
            margin: 0;
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.04em;
        }
        .pay-hero p {
            margin: 8px 0 0;
            color: rgba(255,255,255,0.78);
        }
        .pay-body {
            padding: 24px 24px 30px;
        }
        #paytriframe {
            width: 100%;
            min-height: 620px;
            border-radius: 18px;
            background: #fff;
        }
    </style>
</head>
<body>
<section class="pay-shell">
    <div class="pay-card">
        <div class="pay-hero">
            <h1>{{ __('PAYTR Checkout') }}</h1>
            <p>{{ __('Complete plan payment inside the embedded secure gateway.') }}</p>
        </div>
        <div class="pay-body">
            <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
            <iframe src="https://www.paytr.com/odeme/guvenli/{{$token}}" id="paytriframe" frameborder="0" scrolling="no"></iframe>
            <script>iFrameResize({},'#paytriframe');</script>
        </div>
    </div>
</section>
</body>
</html>
