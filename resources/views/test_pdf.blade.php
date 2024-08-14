<!-- resources/views/test_pdf.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PDF</title>
    <!-- Tambahkan CSS jika diperlukan -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        p {
            text-align: justify;
        }
        .content {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Test PDF Page</h1>
        <p>This is a test page for generating PDFs using Laravel.</p>
        <p>You can customize this content to suit your needs. The content here can be converted into a PDF using a package like `dompdf` or `mpdf`.</p>
    </div>
</body>
</html>
