<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Распределение пополнения</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
        }
        input[type="number"] {
            width: 100px;
        }
    </style>
</head>
<body>
    <h1>Распределение пополнения по счетам</h1>

    <label>
        Сумма пополнения: 
        <input type="number" id="topup-amount" value="0" min="0">
    </label>

    <table id="accounts-table">
        <thead>
            <tr>
                <th>№</th>
                <th>Счет</th>
                <th>Абонплата</th>
                <th>Баланс</th>
                <th>Зачислено</th>
                <th>Итог</th>
                <th>Заморожен</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $i => $acc)
                <tr data-index="{{ $i }}">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $acc['account'] }}</td>
                    <td><input type="number" class="subscription" value="{{ $acc['subscription'] }}"></td>
                    <td><input type="number" class="balance" value="{{ $acc['balance'] }}"></td>
                    <td class="allocated">0</td>
                    <td class="final">0</td>
                    <td>{{ $acc['frozen'] ? 'Да' : 'Нет' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const topupInput = document.getElementById('topup-amount');
        const table = document.getElementById('accounts-table');

        function parseRow(row) {
            return {
                row,
                index: row.dataset.index,
                account: row.children[1].textContent.trim(),
                subscription: parseFloat(row.querySelector('.subscription').value),
                balance: parseFloat(row.querySelector('.balance').value),
                frozen: row.children[6].textContent.trim() === 'Да',
                allocatedCell: row.querySelector('.allocated'),
                finalCell: row.querySelector('.final')
            };
        }

        function calculate() {
            const rows = Array.from(table.querySelectorAll('tbody tr')).map(parseRow);
            let topupAmount = parseFloat(topupInput.value);
            if (isNaN(topupAmount) || topupAmount <= 0) topupAmount = 0;

            rows.forEach(row => {
                row.allocated = 0;
                row.final = row.balance;
            });

            const main = rows.find(r => r.account === '715044' && !r.frozen && r.balance < 0);
            if (main) {
                const toCover = Math.min(-main.balance, topupAmount);
                main.allocated += toCover;
                main.final += toCover;
                topupAmount -= toCover;
            }

            const othersInDebt = rows
                .filter(r => r.account !== '715044' && !r.frozen && r.balance < 0)
                .sort((a, b) => a.balance - b.balance); // от -100 к -1

            for (const r of othersInDebt) {
                if (topupAmount <= 0) break;
                const toCover = Math.min(-r.balance, topupAmount);
                r.allocated += toCover;
                r.final += toCover;
                topupAmount -= toCover;
            }

            const eligible = rows.filter(r => !r.frozen);
            const totalSub = eligible.reduce((sum, r) => sum + r.subscription, 0);

            for (const r of eligible) {
                if (topupAmount <= 0) break;
                const part = totalSub > 0 ? (r.subscription / totalSub) * topupAmount : 0;
                r.allocated += part;
                r.final += part;
            }

            rows.forEach(r => {
                r.allocatedCell.textContent = r.allocated.toFixed(2);
                r.finalCell.textContent = r.final.toFixed(2);
            });
        }

        topupInput.addEventListener('input', calculate);
        document.querySelectorAll('.subscription, .balance').forEach(input => {
            input.addEventListener('input', calculate);
        });

        calculate(); 
    });
</script>

</body>
</html>
