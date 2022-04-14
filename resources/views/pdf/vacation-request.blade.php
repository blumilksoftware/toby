<!DOCTYPE html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Wniosek urlopowy</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        h2 {
            text-align: center;
        }

        p {
            margin: 0;
            text-align: center;
        }

        .container {
            margin: 60px 20px;
        }

        .helper-text {
            font-size: 12px;
            font-weight: bold;
            padding-bottom: 12px;
        }

        .content {
            margin-top: 60px;
            line-height: 24px;
            text-align: left;
        }

        .main {
            margin-top: 60px;
        }

        table {
            width: 100%;
            text-align: center;
        }

        .signatureTable {
            margin-top: 100px;
        }
    </style>
</head>
<body>
<div class="container">

    <div>
        <table>
            <tbody>
            <tr>
                <td>{{ $vacationRequest->user->profile->fullName }}</td>
                <td>Legnica, {{ $vacationRequest->created_at->format("d.m.Y") }}</td>
            </tr>
            <tr>
                <td class="helper-text">imię i nazwisko</td>
            </tr>
            <tr>
                <td>{{ $vacationRequest->user->profile->position }}</td>
            </tr>
            <tr>
                <td class="helper-text">stanowisko</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="main">
        <h2>Wniosek o urlop</h2>
        <p class="content">
            Proszę o {{ mb_strtolower($vacationRequest->type->label()) }} w okresie od dnia {{ $vacationRequest->from->format("d.m.Y") }}
            do dnia {{ $vacationRequest->to->format("d.m.Y") }} włącznie tj. {{ $vacationRequest->vacations()->count() }} dni roboczych za rok {{ $vacationRequest->yearPeriod->year }}.
        </p>
    </div>

    <table class="signatureTable">
        <tbody>
        <tr>
            <td>........................</td>
            <td>........................</td>
        </tr>
        <tr>
            <td class="helper-text">podpis przełożonego</td>
            <td class="helper-text">podpis pracownika</td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
