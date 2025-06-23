import './bootstrap';
import ApexCharts from 'apexcharts';
import Datepicker from 'flowbite-datepicker/Datepicker';
import { Modal } from 'flowbite'; // Pastikan Modal diimpor jika Anda menggunakannya secara eksternal

document.addEventListener("DOMContentLoaded", function () {
    const token = localStorage.getItem("token");

    // Elemen untuk ringkasan donasi (biasanya di dashboard admin)
    const totalDonasiElement = document.getElementById('total-donasi-masuk');
    const totalDonasiBarangElement = document.getElementById('total-donasi-barang'); // Sudah ada
    const totalDonasiUangElement = document.getElementById('total-donasi-uang'); // Sudah ada

    // Perbaikan: Tambahkan elemen untuk menampilkan total donasi barang yang di-accepted/success
    const totalDonasiBarangAcceptedElement = document.getElementById('total-donasi-barang-accepted'); // ID baru untuk span ini

    // Elemen untuk chart (biasanya di dashboard admin)
    const mainChartElement = document.getElementById('main-chart');
    // Perbaikan: Ganti selector untuk chartMonthDisplay agar menargetkan elemen yang berbeda
    // Asumsi: Anda akan menambahkan <span id="chart-period-display"></span> di HTML Anda
    const chartMonthDisplay = document.getElementById('chart-period-display'); 

    // Elemen untuk dropdown filter tanggal (chart, biasanya di dashboard admin)
    const filterDropdownButton = document.querySelector('[data-dropdown-toggle="weekly-sales-dropdown"]');
    const dropdownItems = document.querySelectorAll('#weekly-sales-dropdown a[role="menuitem"]');
    const dropdownDateRangeDisplay = document.querySelector('#weekly-sales-dropdown .px-4.py-3 p');

    let donasiMainChart;
    let allDonationsData = []; // Variabel untuk menyimpan seluruh data donasi yang diambil dari API

    // Elemen input Datepicker (di dashboard admin)
    const startDateInput = document.getElementById('date-range-start'); 
    const endDateInput = document.getElementById('date-range-end');     

    let datepickerRange = null;

    // Fungsi untuk inisialisasi Datepicker
    function initDatepickers() {
        // Cek apakah elemen input tanggal ada, karena mungkin tidak ada di semua role UI
        if (startDateInput && endDateInput && !datepickerRange) { 
            datepickerRange = new Datepicker(startDateInput, {
                autohide: true,
                format: 'yyyy-mm-dd',
            });
            new Datepicker(endDateInput, {
                autohide: true,
                format: 'yyyy-mm-dd',
            });

            startDateInput.addEventListener('changeDate', () => { 
                fetchAndRenderMainChart();
                updateDropdownDateRangeDisplay(startDateInput.value, endDateInput.value);
            });
            endDateInput.addEventListener('changeDate', () => { 
                fetchAndRenderMainChart();
                updateDropdownDateRangeDisplay(endDateInput.value, endDateInput.value); 
            });
        }
    }

    // Fungsi untuk memperbarui tampilan rentang tanggal di dropdown filter
    function updateDropdownDateRangeDisplay(startDateText = null, endDateText = null) {
        if (dropdownDateRangeDisplay && filterDropdownButton) { // Pastikan elemen ada
            if (startDateText && endDateText) {
                dropdownDateRangeDisplay.textContent = `${startDateText} - ${endDateText}`;
                filterDropdownButton.innerHTML = `${startDateText} - ${endDateText} <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
            } else {
                filterDropdownButton.innerHTML = `Last 30 days <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
                dropdownDateRangeDisplay.textContent = 'Last 30 days';
            }
        }
    }

    // Penanganan jika token tidak ditemukan
    if (!token) {
        console.warn('Token tidak ditemukan di localStorage. Beberapa fungsi mungkin tidak berjalan tanpa token.');
        if (totalDonasiElement) {
            totalDonasiElement.textContent = "N/A (Login dibutuhkan)";
        }
        if (totalDonasiBarangElement) totalDonasiBarangElement.textContent = 'N/A';
        if (totalDonasiUangElement) totalDonasiUangElement.textContent = 'N/A';
        if (totalDonasiBarangAcceptedElement) totalDonasiBarangAcceptedElement.textContent = 'N/A'; // Tambahkan ini
        if (mainChartElement) {
            renderMainChart([], [], []); 
        }
        return; 
    }

    // Event listener untuk dropdown filter tanggal (hanya jika elemen ada)
    if (dropdownItems.length > 0) {
        dropdownItems.forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault();
                const filterText = this.textContent.trim();
                
                let start = new Date();
                let end = new Date();

                switch (filterText) {
                    case 'Today':
                        break;
                    case 'Yesterday':
                        start.setDate(start.getDate() - 1);
                        end.setDate(end.getDate() - 1);
                        break;
                    case 'Last 7 days':
                        start.setDate(start.getDate() - 6);
                        break;
                    case 'Last 30 days':
                        start.setDate(start.getDate() - 29);
                        break;
                    case 'Last 90 days':
                        start.setDate(start.getDate() - 89);
                        break;
                    case 'Custom...':
                        initDatepickers(); // Inisialisasi datepicker saat "Custom" dipilih
                        console.log("Untuk 'Custom...', pastikan Anda memiliki input tanggal di HTML.");
                        return; // Jangan lanjutkan fetch chart sampai tanggal custom dipilih
                    default:
                        start.setDate(start.getDate() - 29); // Default ke 30 hari terakhir
                }

                const formatDate = (date) => date.toISOString().split('T')[0];
                if (startDateInput) startDateInput.value = formatDate(start);
                if (endDateInput) endDateInput.value = formatDate(end);

                updateDropdownDateRangeDisplay(filterText === 'Custom...' ? null : filterText, filterText === 'Custom...' ? null : null);
                fetchAndRenderMainChart();
            });
        });
    }

    // Fungsi untuk merender chart utama (ApexCharts)
    function renderMainChart(categories, seriesDataUang, seriesDataBarang) {
        if (!mainChartElement || typeof ApexCharts === 'undefined') {
            console.error("Elemen chart 'main-chart' atau ApexCharts tidak ditemukan.");
            return;
        }

        if (donasiMainChart) {
            donasiMainChart.destroy(); 
        }

        const options = {
            series: [{
                name: "Total Donasi (Uang)",
                data: seriesDataUang,
                color: '#4F46E5', 
            }, {
                name: "Total Donasi (Barang)",
                data: seriesDataBarang,
                color: '#10B981' 
            }],
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: false
                }
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy'
                },
                y: [{
                    formatter: function (value) {
                        return value.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    },
                    title: {
                        formatter: function (seriesName) {
                            return seriesName;
                        }
                    }
                }, {
                    formatter: function (value) {
                        return `${value} unit`; 
                    },
                    title: {
                        formatter: function (seriesName) {
                            return seriesName;
                        }
                    }
                }]
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: categories,
                type: 'datetime',
                labels: {
                    format: 'dd/MM',
                    style: {
                        colors: '#6B7280',
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 500,
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: [{ 
                title: {
                    text: 'Total Donasi (Uang)',
                    style: {
                        color: '#4F46E5'
                    }
                },
                labels: {
                    formatter: function (value) {
                        return value.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    },
                    style: {
                        colors: '#6B7280',
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 500,
                    }
                },
                min: 0,
                axisBorder: {
                    show: true,
                    color: '#4F46E5'
                },
                axisTicks: {
                    show: true,
                }
            }, { 
                opposite: true, 
                title: {
                    text: 'Total Donasi (Barang)',
                    style: {
                        color: '#10B981'
                    }
                },
                labels: {
                    formatter: function (value) {
                        return value.toFixed(0); 
                    },
                    style: {
                        colors: '#6B7280',
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 500,
                    }
                },
                min: 0,
                axisBorder: {
                    show: true,
                    color: '#10B981'
                },
                axisTicks: {
                    show: true,
                }
            }],
            grid: {
                show: true,
                borderColor: '#E5E7EB'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '14px',
                fontFamily: 'Inter, sans-serif',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 12,
                    offsetY: 0
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 0
                }
            },
            markers: {
                size: 0,
                hover: {
                    sizeOffset: 6
                }
            },
            responsive: [
                {
                    breakpoint: 768, 
                    options: {
                        chart: {
                            height: 280
                        },
                        yaxis: [{
                            labels: { show: true }
                        }, {
                            labels: { show: true }
                        }]
                    }
                }
            ]
        };

        donasiMainChart = new ApexCharts(mainChartElement, options);
        donasiMainChart.render();
    }

    // Fungsi untuk mengambil data dan merender chart
    async function fetchAndRenderMainChart() {
        const dailyDonationsUang = {};
        const dailyDonationsBarang = {};

        if (!startDateInput || !endDateInput) {
            console.warn("Elemen input tanggal tidak ditemukan untuk grafik utama.");
            return;
        }

        let startDate = startDateInput.value;
        let endDate = endDateInput.value;

        if (!startDate || !endDate) {
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 29);
            startDate = thirtyDaysAgo.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            startDateInput.value = startDate;
            endDateInput.value = endDate;
            updateDropdownDateRangeDisplay();
        }

        const params = new URLSearchParams();
        params.append('start_date', startDate);
        params.append('end_date', endDate);

        console.log(`Fetching chart data for range: ${startDate} to ${endDate}`);
        console.log(`API URL: /api-proxy/donasi?${params.toString()}`);

        try {
            const response = await fetch(`/api-proxy/donasi?${params.toString()}`, {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Content-Type": "application/json"
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('Unauthorized: Token tidak valid atau kadaluarsa. Harap login ulang.');
                }
                throw new Error('Gagal fetch data donasi untuk chart: ' + response.statusText);
            }

            const result = await response.json();
            const donasiData = result.data;

            console.log('API Response data for chart:', donasiData);

            donasiData.forEach(item => {
                const dateObj = new Date(item.created_at);
                if (isNaN(dateObj)) {
                    console.warn(`Invalid date format for item.created_at: ${item.created_at}. Skipping this item for chart.`);
                    return;
                }
                const date = dateObj.toISOString().split('T')[0]; 
                const qty = Number(item.qty);

                if (!isNaN(qty)) {
                    const currentStatus = item.status_validasi || item.status;
                    if (currentStatus === 'accepted' || currentStatus === 'success') {
                        if (item.type === 'uang') {
                            dailyDonationsUang[date] = (dailyDonationsUang[date] || 0) + qty;
                        } else if (item.type === 'barang') {
                            dailyDonationsBarang[date] = (dailyDonationsBarang[date] || 0) + qty;
                        }
                    }
                } else {
                    console.warn(`Invalid quantity for item: ${item.qty}. Skipping this item for chart.`);
                }
            });

            const categories = [];
            const seriesDataUang = [];
            const seriesDataBarang = [];

            const start = new Date(startDate);
            const end = new Date(endDate);
            let current = new Date(start);

            while (current <= end) {
                const dateString = current.toISOString().split('T')[0];
                categories.push(dateString); 
                seriesDataUang.push(dailyDonationsUang[dateString] || 0);
                seriesDataBarang.push(dailyDonationsBarang[dateString] || 0);
                current.setDate(current.getDate() + 1);
            }

            console.log('Daily Donasi Uang totals (raw):', dailyDonationsUang);
            console.log('Daily Donasi Barang totals (raw):', dailyDonationsBarang);
            console.log('Chart Categories (Dates):', categories);
            console.log('Chart Series Data (Total Donasi Uang per hari):', seriesDataUang);
            console.log('Chart Series Data (Total Donasi Barang per hari):', seriesDataBarang);

            renderMainChart(categories, seriesDataUang, seriesDataBarang);

            if (chartMonthDisplay && categories.length > 0) {
                const dateFromChart = new Date(categories[0]);
                const lastDateFromChart = new Date(categories[categories.length - 1]);
                const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

                if (categories.length === 1) {
                    chartMonthDisplay.textContent = `Pada tanggal: ${categories[0]}`;
                } else if (dateFromChart.getMonth() === lastDateFromChart.getMonth() && dateFromChart.getFullYear() === lastDateFromChart.getFullYear()) {
                    chartMonthDisplay.textContent = `Pada bulan: ${monthNames[dateFromChart.getMonth()]} ${dateFromChart.getFullYear()}`;
                } else {
                    chartMonthDisplay.textContent = `Periode: ${startDate} s/d ${endDate}`;
                }
            } else if (chartMonthDisplay) {
                chartMonthDisplay.textContent = `Periode: Tidak ada data`;
            }

        } catch (error) {
            console.error('Error fetching data for main chart:', error);
            if (mainChartElement) { 
                 renderMainChart([], [], []); 
            }
           
            if (chartMonthDisplay) {
                chartMonthDisplay.textContent = `Periode: Error memuat data`;
            }
        }
    }

    if (token && startDateInput && endDateInput) {
        const defaultStartDate = new Date();
        defaultStartDate.setDate(defaultStartDate.getDate() - 29);
        const defaultEndDate = new Date();
        startDateInput.value = defaultStartDate.toISOString().split('T')[0];
        endDateInput.value = defaultEndDate.toISOString().split('T')[0];
        fetchAndRenderMainChart();
    }

    // --- Bagian untuk fetch semua data donasi untuk tabel dan ringkasan (GET) ---
    fetch('/api-proxy/donasi', {
        method: "GET",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    })
    .then(response => {
        if (!response.ok) {
            if (totalDonasiElement) {
                totalDonasiElement.textContent = "Error memuat data";
            }
            if (totalDonasiBarangElement) totalDonasiBarangElement.textContent = 'Error';
            if (totalDonasiUangElement) totalDonasiUangElement.textContent = 'Error';
            if (totalDonasiBarangAcceptedElement) totalDonasiBarangAcceptedElement.textContent = 'Error'; 
            if (response.status === 401) {
                throw new Error('Unauthorized: Token tidak valid atau kadaluarsa. Harap login ulang.');
            }
            throw new Error('Gagal fetch data donasi: ' + response.statusText);
        }
        return response.json();
    })
    .then(result => {
        const data = result.data;
        allDonationsData = data; 
        const tbody = document.querySelector('#donasi-table tbody'); 
        const donasiTerhimpunList = document.getElementById('donasi-terhimpun'); 

        if (tbody) {
            tbody.innerHTML = '';

            const createButtonExists = document.getElementById('createProductModalButton');
            const isUserTable = createButtonExists ? true : (document.querySelector('#donasi-table th:nth-child(8)') === null); 
            const colspanValue = isUserTable ? 7 : 9; 

            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${colspanValue}" class="px-6 py-4 text-center text-gray-500">Tidak ada data donasi.</td></tr>`; 
                if (donasiTerhimpunList) {
                    donasiTerhimpunList.innerHTML = '<li class="px-6 py-4 text-center text-gray-500">Tidak ada donasi terhimpun.</li>';
                }
                if (totalDonasiElement) {
                    totalDonasiElement.textContent = (0).toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    });
                }
                if (totalDonasiBarangElement) totalDonasiBarangElement.textContent = '0 unit';
                if (totalDonasiUangElement) totalDonasiUangElement.textContent = 'Rp 0';
                if (totalDonasiBarangAcceptedElement) totalDonasiBarangAcceptedElement.textContent = '0 unit'; 
                updateStatusCounts(data); 
                return;
            }

            data.forEach(item => {
                let statusColorClass = 'text-gray-900 dark:text-white';
                const currentStatus = item.status_validasi || item.status; 

                let displayStatus = '-'; 
                if (currentStatus) {
                    displayStatus = currentStatus.replace(/_/g, ' ').toUpperCase();
                }

                switch (currentStatus) { 
                    case 'rejected':
                        statusColorClass = 'text-red-600 font-semibold';
                        break;
                    case 'pending':
                        statusColorClass = 'text-blue-600 font-semibold';
                        break;
                    case 'accepted':
                    case 'success':
                    case 'taken': 
                        statusColorClass = 'text-green-600 font-semibold';
                        break;
                    case 'need_validation':
                        statusColorClass = 'text-yellow-600 font-semibold';
                        break;
                    default:
                        statusColorClass = 'text-gray-900 dark:text-white'; 
                        break;
                }

                let row = `
                    <tr data-status="${currentStatus}"> 
                        <td class="px-6 py-4">${item.id}</td>
                        <td class="px-6 py-4">${item.userid || '-'}</td>
                        <td class="px-6 py-4">${item.type || '-'}</td>
                        <td class="px-6 py-4">${item.qty || 0} ${item.unit || ''}</td>
                        <td class="px-6 py-4">${item.keterangan || '-'}</td>
                        <td class="px-6 py-4 ${statusColorClass}">
                            ${displayStatus}
                        </td>
                        <td class="px-6 py-4">${new Date(item.created_at).toLocaleString()}</td>`;
                
                if (!isUserTable) {
                    row += `
                        <td class="px-6 py-4">
                            <button onclick="editDonasi(${item.id}, '${item.userid}', '${item.type}', ${item.qty}, '${item.unit}', '${item.keterangan}')"
                                title="Edit Donasi" class="text-blue-600 hover:text-blue-800 cursor-pointer mr-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="hapusDonasi(${item.id})" title="Hapus Donasi" class="text-red-600 hover:text-red-800 cursor-pointer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <select onchange="updateDonationStatus(${item.id}, this.value)"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 min-w-[130px]">
                                <option value="">Pilih Status</option>
                                <option value="need_validation" ${currentStatus === 'need_validation' ? 'selected' : ''}>Need Validation</option>
                                <option value="pending" ${currentStatus === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="accepted" ${currentStatus === 'accepted' ? 'selected' : ''}>Accepted</option>
                                <option value="rejected" ${currentStatus === 'rejected' ? 'selected' : ''}>Rejected</option>
                                <option value="taken" ${currentStatus === 'taken' ? 'selected' : ''}>Taken</option>
                                <option value="success" ${currentStatus === 'success' ? 'selected' : ''}>Success</option>
                            </select>
                        </td>`;
                }
                row += `</tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            console.warn("Elemen #donasi-table tbody tidak ditemukan. Tabel donasi mungkin tidak akan ditampilkan.");
        }


        // --- RENDER DONASI TERHIMPUN (ACCEPTED/SUCCESS STATUS ONLY) ---
        if (donasiTerhimpunList) {
            donasiTerhimpunList.innerHTML = ''; 

            const acceptedDonations = data.filter(item => 
                (item.status_validasi === 'accepted' || item.status_validasi === 'success')
            );

            if (acceptedDonations.length === 0) {
                donasiTerhimpunList.innerHTML = '<li class="px-6 py-4 text-center text-gray-500">Tidak ada donasi terhimpun dengan status Accepted/Success.</li>';
            } else {
                acceptedDonations.forEach(item => {
                    const listItem = `
                        <li class="py-3 sm:py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <span class="p-2.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        ${item.type === 'uang' ? '<i class="fas fa-money-bill-wave"></i>' : '<i class="fas fa-box"></i>'}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                        ${item.userid || 'Anonim'}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                        ${item.keterangan || 'Tanpa keterangan'}
                                    </p>
                                </div>
                                <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                    ${item.type === 'uang' ? item.qty.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }) : `${item.qty} ${item.unit || ''}`}
                                </div>
                            </div>
                        </li>
                    `;
                    donasiTerhimpunList.insertAdjacentHTML('beforeend', listItem);
                });
            }
        } else {
            console.error("Elemen #donasi-terhimpun tidak ditemukan.");
        }

        // --- Hitung dan tampilkan total donasi Rupiah (untuk total donasi masuk) ---
        // Ini akan menjumlahkan HANYA donasi UANG dengan status accepted atau success
        const totalDonasiRupiahAcceptedUang = data.reduce((sum, item) => {
            const currentStatus = item.status_validasi || item.status;
            if (item.type === 'uang' && typeof item.qty === 'number' && !isNaN(item.qty) && 
                (currentStatus === 'accepted' || currentStatus === 'success')) {
                return sum + item.qty;
            }
            return sum;
        }, 0);

        if (totalDonasiElement) {
            totalDonasiElement.textContent = totalDonasiRupiahAcceptedUang.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0, 
                maximumFractionDigits: 0 
            });
        }

        // --- HITUNG DATA UNTUK RINGKASAN Uang vs Barang (ACCEPTED/SUCCESS STATUS ONLY) ---
        // Ini akan menjumlahkan uang dan barang yang statusnya accepted atau success
        let totalUangAcceptedSummary = 0; 
        let totalBarangAcceptedSummary = 0; 
        let totalBarangAccepted = 0; // Inisialisasi untuk total donasi barang yang accepted

        data.filter(item => {
            const currentStatus = item.status_validasi || item.status;
            return (currentStatus === 'accepted' || currentStatus === 'success');
        }).forEach(item => { 
            if (item.type === 'uang' && typeof item.qty === 'number' && !isNaN(item.qty)) {
                totalUangAcceptedSummary += item.qty;
            } else if (item.type === 'barang' && typeof item.qty === 'number' && !isNaN(item.qty)) {
                totalBarangAcceptedSummary += item.qty;
                totalBarangAccepted += item.qty; // Hitung juga untuk elemen baru
            }
        });

        // Update UI text for Barang and Uang
        if (totalDonasiBarangElement) {
            totalDonasiBarangElement.textContent = totalBarangAcceptedSummary.toLocaleString('id-ID') + ' unit';
        }
        if (totalDonasiUangElement) {
            totalDonasiUangElement.textContent = totalUangAcceptedSummary.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
        }
        // Perbaikan: Update elemen baru untuk total donasi barang yang di-accepted
        if (totalDonasiBarangAcceptedElement) {
            totalDonasiBarangAcceptedElement.textContent = totalBarangAccepted.toLocaleString('id-ID') + ' unit';
        }


        // --- Panggil updateStatusCounts di akhir blok .then ini ---
        updateStatusCounts(data); 

    })
    .catch(error => {
        console.error('Error fetching all donasi data:', error);
        // Tampilkan pesan error di elemen-elemen total
        if (totalDonasiElement) {
            totalDonasiElement.textContent = "Error: " + error.message;
        }
        if (totalDonasiBarangElement) totalDonasiBarangElement.textContent = 'Error';
        if (totalDonasiUangElement) totalDonasiUangElement.textContent = 'Error';
        if (totalDonasiBarangAcceptedElement) totalDonasiBarangAcceptedElement.textContent = 'Error'; 
        
        // Juga, tampilkan pesan error di tabel dan daftar terhimpun
        const tbody = document.querySelector('#donasi-table tbody');
        const createButtonExists = document.getElementById('createProductModalButton');
        const isUserTable = createButtonExists ? true : (document.querySelector('#donasi-table th:nth-child(8)') === null); 
        const colspanValue = isUserTable ? 7 : 9; 

        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="${colspanValue}" class="px-6 py-4 text-center text-red-500">Gagal memuat data donasi.</td></tr>`; 
        }
        const donasiTerhimpunList = document.getElementById('donasi-terhimpun');
        if (donasiTerhimpunList) {
            donasiTerhimpunList.innerHTML = '<li class="px-6 py-4 text-center text-red-500">Gagal memuat donasi terhimpun.</li>';
        }
        updateStatusCounts([]); 
    });

    // --- Modal 'Add Donation' dan Form Handling (Untuk User Role) ---
    const createProductModalElement = document.getElementById('createProductModal');
    let createProductModal;
    if (createProductModalElement) {
        createProductModal = new Modal(createProductModalElement, {
            backdrop: 'static',
            backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
            closable: true,
        });

        const createProductModalButton = document.getElementById('createProductModalButton');
        if (createProductModalButton) {
            createProductModalButton.addEventListener('click', () => {
                createProductModal.show();
            });
        } else {
            console.warn("Tombol 'createProductModalButton' tidak ditemukan.");
        }
    } else {
        console.warn("Elemen modal 'createProductModal' tidak ditemukan.");
    }

    const createDonationForm = document.getElementById('createDonationForm');
    if (createDonationForm) {
        createDonationForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const newDonationData = {
                userid: document.getElementById('userid').value,
                qty: parseFloat(document.getElementById('qty').value),
                type: document.getElementById('type').value,
                unit: document.getElementById('unit').value,
                keterangan: document.getElementById('keterangan').value,
            };

            fetch(`/api-proxy/donasi`, { 
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(newDonationData),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal membuat donasi: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                alert('Donasi berhasil ditambahkan!');
                if (createProductModal) {
                    createProductModal.hide();
                }
                createDonationForm.reset(); 
                location.reload(); 
            })
            .catch(error => {
                console.error('Error creating donation:', error);
                alert('Gagal menambahkan donasi: ' + error.message);
            });
        });
    } else {
        console.warn("Form 'createDonationForm' tidak ditemukan.");
    }


    // --- Donation Edit Modal Logic ---
    const editDonationModalElement = document.getElementById('editDonationModal');
    let editDonationModal;
    if (editDonationModalElement) {
        editDonationModal = new Modal(editDonationModalElement, {
            backdrop: 'static',
            backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
            closable: true,
        });
    } else {
        console.warn("Elemen modal 'editDonationModal' tidak ditemukan.");
    }

    // Fungsi untuk membuka modal edit dan mengisi data
    window.editDonasi = function(id, userid, type, qty, unit, keterangan) {
        if (editDonationModalElement) {
            document.getElementById('edit-donasi-id').value = id;
            document.getElementById('edit-userid').value = userid;
            document.getElementById('edit-type').value = type;
            document.getElementById('edit-qty').value = qty;
            document.getElementById('edit-unit').value = unit;
            document.getElementById('edit-keterangan').value = keterangan;
            editDonationModal.show();
        } else {
            alert("Modal edit donasi tidak dapat dibuka karena elemen tidak ditemukan.");
        }
    };

    const editDonationForm = document.getElementById('editDonationForm');
    if (editDonationForm) {
        editDonationForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const donasiId = document.getElementById('edit-donasi-id').value;
            
            // Perbaikan: Ambil nilai qty dan pastikan itu adalah angka yang valid
            let qtyValue = parseFloat(document.getElementById('edit-qty').value);
            if (isNaN(qtyValue)) {
                qtyValue = 0; // Default ke 0 jika bukan angka yang valid
                console.warn("Kuantitas (qty) yang dimasukkan bukan angka. Diatur ke 0.");
            }

            const updatedData = {
                userid: document.getElementById('edit-userid').value,
                type: document.getElementById('edit-type').value,
                qty: qtyValue, // Gunakan nilai yang sudah dipastikan angka
                unit: document.getElementById('edit-unit').value,
                keterangan: document.getElementById('edit-keterangan').value,
            };

            fetch(`/api-proxy/donasi/${donasiId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(updatedData),
            })
            .then(response => {
                if (!response.ok) {
                    // Perbaikan: Parse error dari server untuk edit donasi juga
                    if (response.status === 422) {
                        return response.json().then(err => {
                            const errorMessage = err.message || JSON.stringify(err.errors || err);
                            throw new Error(`Edit Donasi Gagal: ${errorMessage}`);
                        });
                    }
                    throw new Error('Failed to update donation: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                alert('Donasi berhasil diperbarui!');
                if (editDonationModal) {
                    editDonationModal.hide();
                }
                location.reload(); 
            })
            .catch(error => {
                console.error('Error updating donation:', error);
                alert('Gagal memperbarui donasi: ' + error.message);
            });
        });
    } else {
        console.warn("Form 'editDonationForm' tidak ditemukan.");
    }

    // Handle donation deletion
    window.hapusDonasi = function(donasiId) {
        if (!confirm('Apakah Anda yakin ingin menghapus donasi ini?')) {
            return;
        }

        fetch(`/api-proxy/donasi/${donasiId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete donation: ' + response.statusText);
            }
            alert('Donasi berhasil dihapus!');
            location.reload(); 
        })
        .catch(error => {
            console.error('Error deleting donation:', error);
            alert('Gagal menghapus donasi: ' + error.message);
        });
    };

    // Handle update status donasi
    window.updateDonationStatus = function(donasiId, newStatus) {
        if (!newStatus) {
            console.log("Tidak ada status yang dipilih, tidak memperbarui.");
            return;
        }

        let catatanValidasi = '';
        // Perbaikan: Prompt catatan validasi jika status berubah ke 'accepted', 'rejected', 'pending', 'taken', 'success', atau 'need_validation'
        // Memastikan catatan tidak kosong dengan loop
        if (['accepted', 'rejected', 'pending', 'taken', 'success', 'need_validation'].includes(newStatus)) {
            let inputCatatan;
            let firstAttempt = true; 
            do {
                // Pesan prompt lebih eksplisit
                inputCatatan = prompt(`Masukkan catatan untuk validasi donasi ID ${donasiId} (wajib dan tidak boleh kosong untuk status ${newStatus}):${firstAttempt ? '' : '\nCatatan tidak boleh kosong!'}`);
                if (inputCatatan === null) { // Jika user mengklik Cancel
                    location.reload(); // Muat ulang untuk mengembalikan nilai dropdown
                    return;
                }
                firstAttempt = false; 
            } while (inputCatatan.trim() === ''); 
            catatanValidasi = inputCatatan.trim();
        }

        const validatorName = localStorage.getItem('userName') || 'Admin Sistem';

        const requestBody = {
            id_donasi: donasiId,
            status_validasi: newStatus,
            catatan_validasi: catatanValidasi,
            validator: validatorName
        };

        fetch(`/api-proxy/validasi-donasi/admin/validasibyadmin`, { 
            method: 'PUT', 
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                "Accept": "application/json"
            },
            body: JSON.stringify(requestBody),
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('Unauthorized: Token tidak valid atau kadaluarsa. Harap login ulang.');
                }
                if (response.status === 422) {
                    return response.json().then(err => {
                        const errorMessage = err.message || JSON.stringify(err.errors || err);
                        throw new Error(`Validasi Gagal: ${errorMessage}`);
                    });
                }
                throw new Error('Gagal memperbarui status donasi: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            alert('Status donasi berhasil diperbarui!');
            location.reload(); 
        })
        .catch(error => {
            console.error('Error updating donation status:', error);
            alert('Gagal memperbarui status donasi: ' + error.message);
            location.reload(); 
        });
    };


    // --- Logika Filter Status Tabel ---
    const statusCheckboxes = document.querySelectorAll('#status-filter-dropdown input[type="checkbox"]');

    function filterDonationsByStatus() {
        const selectedStatuses = Array.from(statusCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        const tableRows = document.querySelectorAll('#donasi-table tbody tr');

        tableRows.forEach(row => {
            const rowStatus = row.dataset.status; 
            if (selectedStatuses.length === 0 || selectedStatuses.includes(rowStatus)) {
                row.style.display = ''; 
            } else {
                row.style.display = 'none'; 
            }
        });
    }

    statusCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterDonationsByStatus);
    });

    // --- Fungsi untuk memperbarui hitungan status di filter dropdown ---
    function updateStatusCounts(donations) {
        const counts = {
            accepted: 0,
            rejected: 0,
            pending: 0,
            taken: 0,
            need_validation: 0, 
            success: 0 
        };

        donations.forEach(item => {
            const status = item.status_validasi || item.status; 
            if (counts.hasOwnProperty(status)) {
                counts[status]++;
            }
        });

        if (document.getElementById('count-accepted')) document.getElementById('count-accepted').textContent = counts.accepted;
        if (document.getElementById('count-rejected')) document.getElementById('count-rejected').textContent = counts.rejected;
        if (document.getElementById('count-pending')) document.getElementById('count-pending').textContent = counts.pending;
        if (document.getElementById('count-taken')) document.getElementById('count-taken').textContent = counts.taken;
    }
});
