
    {{-- Header --}}
 
    {{-- Row: MCO Summary + Filter Tabs --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h5 class="mb-1 h5">MCO Performance</h5>
                            <p class="text-muted mb-0 small">Total MCO value and records by period</p>
                        </div>
                        <ul class="nav nav-pills mt-3 mt-md-0" id="mcoFilterTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-period="today">Today</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-period="week">This Week</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-period="month">This Month</button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body">
                    {{-- MCO KPI boxes --}}
                    <div class="row text-center mb-4" id="mcoKpiRow">
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="p-3 rounded-3 bg-light">
                                <p class="text-muted mb-1">MCO Records</p>
                                <h4 class="mb-0" id="mcoCount">0</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="p-3 rounded-3 bg-light">
                                <p class="text-muted mb-1">Total MCO Value</p>
                                <h4 class="mb-0" id="mcoTotal">$0.00</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-light">
                                <p class="text-muted mb-1">Avg. MCO / Booking</p>
                                <h4 class="mb-0" id="mcoAvg">$0.00</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-light">
                                <p class="text-muted mb-1">Conversion Rate</p>
                                <h4 class="mb-0" id="mcoConversion">0%</h4>
                            </div>
                        </div>
                    </div>

                    {{-- MCO Chart --}}
                    <div class="position-relative" style="min-height: 260px;">
                        <canvas id="mcoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row: Top Agents --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h5 class="mb-1">Top Performing Agents (This Month)</h5>
                        <p class="text-muted mb-0">Based on total bookings and MCO generated</p>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success d-inline-flex align-items-center mt-3 mt-md-0">
                        <i class="bi bi-trophy-fill me-1"></i> Leaderboard
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Agent Name</th>
                                    <th>Alias</th>
                                    <th class="text-center">Total Bookings</th>
                                    <th class="text-center">MCO Generated</th>
                                </tr>
                            </thead>
                            <tbody id="topAgentsTableBody">
                                {{-- Dummy rows (will be populated by JS) --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Dummy data sets for MCO by period
        const mcoDataSets = {
            today: {
                labels: ['09:00', '11:00', '13:00', '15:00', '17:00'],
                values: [2, 3, 1, 4, 2],
                count: 12,
                total: 3200,
                avg: 266.67,
                conversion: 42
            },
            week: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                values: [5, 7, 4, 9, 8, 3, 2],
                count: 38,
                total: 11200,
                avg: 294.74,
                conversion: 37
            },
            month: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                values: [24, 31, 27, 35],
                count: 117,
                total: 36500,
                avg: 311.97,
                conversion: 39
            }
        };

        // Dummy top agents data (this month)
        const topAgentsDummy = [
            { name: 'Prashant', alias: 'AG3823', bookings: 42, mco: 9800 },
            { name: 'MIS Main', alias: 'AG4125', bookings: 35, mco: 8400 },
            { name: 'Charging Team', alias: 'AG6861', bookings: 29, mco: 7600 },
            { name: 'Support Team', alias: 'AG4084', bookings: 21, mco: 5300 },
        ];

        // Helpers to format numbers
        function formatCurrency(value) {
            return '$' + Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formatPercent(value) {
            return Number(value).toFixed(0) + '%';
        }

        // Init top agents table
        function renderTopAgentsTable() {
            const tbody = document.getElementById('topAgentsTableBody');
            tbody.innerHTML = '';

            topAgentsDummy.forEach((agent, index) => {
                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${agent.name}</td>
                    <td>${agent.alias}</td>
                    <td class="text-center">${agent.bookings}</td>
                    <td class="text-center">${formatCurrency(agent.mco)}</td>
                `;

                tbody.appendChild(tr);
            });
        }

        // Init MCO chart
        let mcoChartInstance = null;

        function renderMcoChart(periodKey) {
            const ctx = document.getElementById('mcoChart').getContext('2d');
            const dataSet = mcoDataSets[periodKey];

            // Update KPI cards
            document.getElementById('mcoCount').textContent = dataSet.count;
            document.getElementById('mcoTotal').textContent = formatCurrency(dataSet.total);
            document.getElementById('mcoAvg').textContent = formatCurrency(dataSet.avg);
            document.getElementById('mcoConversion').textContent = formatPercent(dataSet.conversion);

            const chartConfig = {
                type: 'line',
                data: {
                    labels: dataSet.labels,
                    datasets: [{
                        label: 'MCO Value',
                        data: dataSet.values,
                        borderColor: 'rgba(13, 110, 253, 1)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + formatCurrency(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            };

            if (mcoChartInstance) {
                mcoChartInstance.destroy();
            }
            mcoChartInstance = new Chart(ctx, chartConfig);
        }

        // Tab switching logic
        function initMcoTabs() {
            const tabs = document.querySelectorAll('#mcoFilterTabs .nav-link');

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const period = this.getAttribute('data-period');
                    renderMcoChart(period);
                });
            });

            // Initial load
            renderMcoChart('today');
        }

        document.addEventListener('DOMContentLoaded', function () {
            renderTopAgentsTable();
            initMcoTabs();
        });
    </script>
@endpush