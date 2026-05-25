<style>
    body {
        margin: 0;
        background: #f8fafc;
        color: #111827;
        font-family: Arial, sans-serif;
    }

    .print-container {
        width: min(1100px, calc(100% - 32px));
        margin: 24px auto;
        padding: 28px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .report-actions {
        width: min(1100px, calc(100% - 32px));
        margin: 24px auto 0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .report-actions a,
    .report-actions button {
        border: 1px solid #047857;
        border-radius: 8px;
        background: #ffffff;
        color: #065f46;
        cursor: pointer;
        font-size: 14px;
        font-weight: 700;
        padding: 10px 14px;
        text-decoration: none;
    }

    .report-actions button {
        background: #047857;
        color: #ffffff;
    }

    .print-title {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .print-meta {
        font-size: 13px;
        margin-bottom: 16px;
    }

    .report-brand {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin: 22px 0;
    }

    .summary-card {
        border: 1px solid #d1d5db;
        padding: 12px;
    }

    .summary-card span {
        display: block;
        color: #4b5563;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .summary-card strong {
        display: block;
        font-size: 18px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #cccccc;
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }

    th {
        background: #f2f2f2;
        font-weight: bold;
    }

    .text-right {
        text-align: right;
    }

    .empty-row {
        color: #4b5563;
        text-align: center;
    }

    @media (max-width: 760px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-family: Arial, sans-serif;
        }

        .sidebar,
        .topbar,
        .navbar,
        .no-print,
        .print-hide {
            display: none !important;
        }

        .print-container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none !important;
            border: none !important;
            background: #ffffff !important;
            color: #000000 !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cccccc;
            padding: 8px;
            color: #000000 !important;
        }

        th {
            background: #f2f2f2 !important;
            font-weight: bold;
        }

        .print-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .print-meta {
            font-size: 13px;
            margin-bottom: 16px;
        }
    }
</style>
