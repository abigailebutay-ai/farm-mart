<style>
    body {
        margin: 0;
        background: #eef2f7;
        color: #111827;
        font-family: Arial, sans-serif;
        font-size: 14px;
        line-height: 1.5;
    }

    .print-container {
        width: min(1100px, calc(100% - 32px));
        margin: 24px auto;
        padding: 32px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
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

    .print-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
        border-bottom: 2px solid #047857;
        padding-bottom: 14px;
        margin-bottom: 22px;
    }

    .print-brand {
        font-size: 24px;
        font-weight: 800;
        color: #047857;
    }

    .print-subtitle {
        font-size: 11px;
        letter-spacing: 0.08em;
        color: #374151;
        text-transform: uppercase;
    }

    .print-header-meta {
        text-align: right;
    }

    .print-title {
        font-size: 21px;
        font-weight: 800;
        margin-bottom: 4px;
        color: #111827;
    }

    .print-meta {
        font-size: 12px;
        color: #4b5563;
    }

    .print-summary,
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }

    .print-summary-card,
    .summary-card {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
        background: #f9fafb;
    }

    .print-summary-label,
    .summary-card span {
        display: block;
        font-size: 10px;
        text-transform: uppercase;
        color: #6b7280;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    .print-summary-value,
    .summary-card strong {
        display: block;
        margin-top: 4px;
        font-size: 18px;
        font-weight: 800;
        color: #111827;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }

    th {
        background: #ecfdf5;
        color: #065f46;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.04em;
    }

    th,
    td {
        border: 1px solid #d1d5db;
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }

    td {
        color: #111827;
    }

    .text-right {
        text-align: right;
    }

    .empty-row {
        color: #4b5563;
        text-align: center;
    }

    .print-total-row {
        font-weight: 800;
        background: #f3f4f6;
    }

    .print-footer {
        margin-top: 24px;
        padding-top: 12px;
        border-top: 1px solid #d1d5db;
        text-align: center;
        font-size: 11px;
        color: #6b7280;
    }

    .print-footer p {
        margin: 2px 0;
    }

    @media (max-width: 760px) {
        .print-header {
            flex-direction: column;
        }

        .print-header-meta {
            text-align: left;
        }

        .print-summary,
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        @page {
            size: A4;
            margin: 16mm;
        }

        body {
            background: #ffffff !important;
            color: #111827 !important;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .sidebar,
        .topbar,
        .navbar,
        .no-print,
        .print-hide,
        button {
            display: none !important;
        }

        .print-container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
            background: #ffffff !important;
            color: #111827 !important;
            box-shadow: none !important;
            border: none !important;
        }

        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #047857;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .print-brand {
            font-size: 22px;
            font-weight: 800;
            color: #047857 !important;
        }

        .print-subtitle {
            font-size: 11px;
            letter-spacing: 0.08em;
            color: #374151 !important;
            text-transform: uppercase;
        }

        .print-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #111827 !important;
        }

        .print-meta {
            font-size: 12px;
            color: #4b5563 !important;
        }

        .print-summary,
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .print-summary-card,
        .summary-card {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px;
            background: #f9fafb !important;
        }

        .print-summary-label,
        .summary-card span {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280 !important;
            font-weight: 700;
        }

        .print-summary-value,
        .summary-card strong {
            font-size: 18px;
            font-weight: 800;
            color: #111827 !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: #ecfdf5 !important;
            color: #065f46 !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.04em;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        td {
            color: #111827 !important;
        }

        .text-right {
            text-align: right;
        }

        .print-total-row {
            font-weight: 800;
            background: #f3f4f6 !important;
        }

        .print-footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 11px;
            color: #6b7280 !important;
        }

        a[href]::after {
            content: "";
        }
    }
</style>
