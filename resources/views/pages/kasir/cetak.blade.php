<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice #{{ $invoice['no'] }}</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      font-size: 14px;
      color: #333;
      background: #fff;
    }
    .invoice-box {
      max-width: 900px;
      margin: auto;
      padding: 10px;
      border: 1px solid #eee;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      background: #fff;
    }
    .header {
      margin-bottom: 32px;
      border-bottom: 2px solid #e0e0e0;
      padding-bottom: 18px;
      width: 100%;
    }
    .header-table {
      width: 100%;
      border-collapse: collapse;
    }
    .header-table td {
      padding: 0;
      border: none;
      vertical-align: top;
    }
    .company-info {
      width: 60%;
    }
    .company-info h2 {
      margin: 0 0 6px 0;
      font-size: 1.5rem;
      font-weight: bold;
      color: #1a237e;
      letter-spacing: 1px;
    }
    .company-info .meta {
      font-size: 13px;
      color: #555;
      margin-bottom: 2px;
    }
    .invoice-info {
      width: 40%;
      text-align: right;
    }
    .invoice-info .title {
      font-size: 2rem;
      font-weight: bold;
      color: #1976d2;
      margin-bottom: 2px;
    }
    .invoice-info .invoice-id {
      font-size: 1.1rem;
      font-weight: bold;
      color: #333;
      margin-bottom: 2px;
    }
    .invoice-info .date {
      font-size: 13px;
      color: #555;
    }
    .billing-info {
      display: flex;
      justify-content: space-between;
      margin: 28px 0 10px 0;
    }
    .billing-info > div {
      width: 48%;
    }
    .billing-info .label {
      font-weight: bold;
      margin-bottom: 4px;
      color: #333;
    }
    .billing-info .value {
      margin-bottom: 2px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 18px 0 0 0;
    }
    table th, table td {
      padding: 10px 8px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    table th {
      background: #f5f7fa;
      font-weight: bold;
      font-size: 14px;
      color: #222;
    }
    .totals {
      width: 320px;
      float: right;
      margin-top: 18px;
    }
    .totals table {
      width: 100%;
      margin: 0;
    }
    .totals table td {
      border: none;
      padding: 6px 8px;
      font-size: 14px;
    }
    .totals .total-row td {
      font-weight: bold;
      font-size: 16px;
      border-top: 2px solid #e0e0e0;
    }
    .totals .unpaid {
      color: #e53935;
      font-weight: bold;
    }
    .totals .paid {
      color: #388e3c;
      font-weight: bold;
    }
    .payment-info {
      margin-top: 36px;
      border-top: 1px solid #eee;
      padding-top: 18px;
      font-size: 13px;
    }
    .footer {
      margin-top: 36px;
      text-align: center;
      color: #888;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="invoice-box">
    <div class="header">
      <table class="header-table">
        <tr>
          <td class="company-info">
            <h2>PT PRINT MEDIA INDONESIA</h2>
            <div class="meta">Jl. Percetakan Raya No. 123, Jakarta Selatan</div>
            <div class="meta">Telp: (021) 555-1234 | Email: info@printmedia.co.id</div>
          </td>
          <td class="invoice-info">
            <div class="title">INVOICE</div>
            <div class="invoice-id">{{ $invoice['no'] }}</div>
            <div class="date">Tanggal: {{ $invoice['tanggal'] }}</div>
          </td>
        </tr>
      </table>
    </div>

    <div class="billing-info">
      <div>
        <div class="label">Ditagihkan Kepada:</div>
        <div class="value" style="font-weight:bold;">{{ $invoice['customer']['nama'] }}</div>
        <div class="value">{{ $invoice['customer']['email'] }}</div>
        <div class="value">{{ $invoice['customer']['telp'] }}</div>
      </div>
      <br>
      <div>
        <div class="label">Detail Invoice:</div>
        <div class="value"><b>Nomor Invoice:</b> {{ $invoice['no'] }}</div>
        <div class="value"><b>Tanggal Invoice:</b> {{ $invoice['tanggal'] }}</div>
        <div class="value"><b>Jatuh Tempo:</b> {{ $invoice['jatuh_tempo'] }}</div>
        <div class="value"><b>No. SPK:</b> {{ $invoice['spk_no'] }}</div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width:40%;">Deskripsi</th>
          <th>Jumlah</th>
          <th>Harga Satuan</th>
          <th>Pajak</th>
          <th>Diskon</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoice['items'] as $item)
        <tr>
          <td>{{ $item['deskripsi'] }}</td>
          <td>{{ $item['jumlah'] }}</td>
          <td>Rp {{ number_format($item['harga'],0,',','.') }}</td>
          <td>{{ $item['pajak'] }}</td>
          <td>{{ $item['diskon'] }}</td>
          <td>Rp {{ number_format($item['total'],0,',','.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="totals">
      <table>
        <tr>
          <td>Subtotal:</td>
          <td>Rp {{ number_format($invoice['ringkasan']['subtotal'],0,',','.') }}</td>
        </tr>
        <tr>
          <td>Pajak:</td>
          <td>Rp {{ number_format($invoice['ringkasan']['pajak'],0,',','.') }}</td>
        </tr>
        <tr>
          <td>Diskon:</td>
          <td>-Rp {{ number_format($invoice['ringkasan']['diskon'],0,',','.') }}</td>
        </tr>
        <tr class="total-row">
          <td>Total:</td>
          <td>Rp {{ number_format($invoice['ringkasan']['total'],0,',','.') }}</td>
        </tr>
        <tr>
          <td>Dibayar:</td>
          <td class="paid">Rp {{ number_format($invoice['ringkasan']['dibayar'],0,',','.') }}</td>
        </tr>
        <tr>
          <td>Sisa:</td>
          <td class="unpaid">Rp {{ number_format($invoice['ringkasan']['sisa'],0,',','.') }}</td>
        </tr>
      </table>
    </div>

    <div style="clear:both;"></div>

    <div class="payment-info">
      <p><b>Catatan:</b> {{ $invoice['customer']['catatan'] }}</p>
      <p><b>Instruksi Pembayaran:</b></p>
      <p>Pembayaran dapat dilakukan via transfer bank ke rekening berikut:</p>
      <p>
        <b>Bank Mandiri</b><br>
        No. Rekening: 123-456-789-0<br>
        Atas Nama: PT PRINT MEDIA INDONESIA<br>
      </p>
    </div>

    <div class="footer">
      <p>Terima kasih atas kepercayaan Anda menggunakan jasa kami.<br>
      Jika ada pertanyaan terkait invoice ini, silakan hubungi kami di (021) 555-1234.</p>
      <p>PT PRINT MEDIA INDONESIA</p>
    </div>
  </div>
</body>
</html> 