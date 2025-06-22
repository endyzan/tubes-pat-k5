import './bootstrap';
import ApexCharts from 'apexcharts';
import Datepicker from 'flowbite-datepicker/Datepicker';

document.addEventListener("DOMContentLoaded", function () {
    const token = localStorage.getItem("token");

    const totalDonasiElement = document.getElementById('total-donasi-masuk');
    const totalDonasiBarangElement = document.getElementById('total-donasi-barang');
    const totalDonasiUangElement = document.getElementById('total-donasi-uang');

    const mainChartElement = document.getElementById('main-chart');
    const chartMonthDisplay = document.querySelector('.p-4 > div > h3');

    const filterDropdownButton = document.querySelector('[data-dropdown-toggle="weekly-sales-dropdown"]');
    const dropdownItems = document.querySelectorAll('#weekly-sales-dropdown a[role="menuitem"]');
    const customFilterLink = document.querySelector('#weekly-sales-dropdown a[role="menuitem"][class*="Custom"]');
    const dropdownDateRangeDisplay = document.querySelector('#weekly-sales-dropdown .px-4.py-3 p');

    let donasiMainChart;

    let startDateInput = document.createElement('input');
    startDateInput.type = 'hidden';
    let endDateInput = document.createElement('input');
    endDateInput.type = 'hidden';

    let datepickerRange = null;

    function initDatepickers() {
        if (!datepickerRange) {
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
                updateDropdownDateRangeDisplay(startDateInput.value, endDateInput.value);
            });
        }
    }

    function updateDropdownDateRangeDisplay(startDateText = null, endDateText = null) {
        if (startDateText && endDateText) {
            dropdownDateRangeDisplay.textContent = `${startDateText} - ${endDateText}`;
            filterDropdownButton.innerHTML = `${startDateText} - ${endDateText} <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
        } else {
            filterDropdownButton.innerHTML = `Last 30 days <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
            dropdownDateRangeDisplay.textContent = 'Last 30 days';
        }
    }

    if (!token) {
        console.warn('Token tidak ditemukan di localStorage. Beberapa fungsi mungkin tidak berjalan tanpa token.');
        if (totalDonasiElement) {
            totalDonasiElement.textContent = "N/A (Login dibutuhkan)";
        }
        if (totalDonasiBarangElement) totalDonasiBarangElement.textContent = 'N/A';
        if (totalDonasiUangElement) totalDonasiUangElement.textContent = 'N/A';
        // Render chart dengan data kosong untuk menghindari error
        renderMainChart([], [], []); 
        return;
    }

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
                    initDatepickers();
                    console.log("Untuk 'Custom...', pastikan Anda memiliki input tanggal di HTML dan inisialisasi Datepicker pada input tersebut.");
                    return;
                default:
                    start.setDate(start.getDate() - 29);
            }

            const formatDate = (date) => date.toISOString().split('T')[0];
            startDateInput.value = formatDate(start);
            endDateInput.value = formatDate(end);

            updateDropdownDateRangeDisplay(filterText === 'Custom...' ? null : filterText, filterText === 'Custom...' ? null : null);
            fetchAndRenderMainChart();
        });
    });

    // Mengubah fungsi renderMainChart untuk menerima dua series data
    function renderMainChart(categories, seriesDataUang, seriesDataBarang) {
        if (!mainChartElement) {
            console.error("Elemen chart 'main-chart' tidak ditemukan.");
            return;
        }

        if (donasiMainChart) {
            donasiMainChart.destroy();
        }

        const options = {
            series: [{
                name: "Total Donasi (Uang)",
                data: seriesDataUang,
                color: '#4F46E5', // Warna untuk donasi uang
            }, {
                name: "Total Donasi (Barang)",
                data: seriesDataBarang,
                color: '#10B981' // Warna untuk donasi barang (contoh hijau)
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
                    // Tooltip untuk sumbu Y pertama (Uang)
                    formatter: function (value) {
                        return value.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    },
                    title: {
                        formatter: function (seriesName) {
                            return seriesName;
                        }
                    }
                }, {
                    // Tooltip untuk sumbu Y kedua (Barang)
                    formatter: function (value) {
                        return `${value} unit`; // Menampilkan sebagai unit barang
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
            yaxis: [{ // Sumbu Y pertama (untuk uang)
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
            }, { // Sumbu Y kedua (untuk barang)
                opposite: true, // Posisikan di sisi berlawanan
                title: {
                    text: 'Total Donasi (Barang)',
                    style: {
                        color: '#10B981'
                    }
                },
                labels: {
                    formatter: function (value) {
                        return value.toFixed(0); // Barang biasanya bilangan bulat
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
                show: true, // Grid perlu diaktifkan agar sumbu Y kedua terlihat jelas
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
                show: true, // Tampilkan legend agar tahu garis mana untuk apa
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
                    breakpoint: 768, // Untuk layar kecil
                    options: {
                        chart: {
                            height: 280
                        },
                        yaxis: [{
                            labels: {
                                show: true // Pastikan label Y-axis tetap terlihat di mobile
                            }
                        }, {
                            labels: {
                                show: true
                            }
                        }]
                    }
                }
            ]
        };

        donasiMainChart = new ApexCharts(mainChartElement, options);
        donasiMainChart.render();
    }

    async function fetchAndRenderMainChart() {
        const token = localStorage.getItem("token");
        if (!token) {
            console.error("Token tidak ditemukan untuk fetch chart data.");
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
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

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

            const dailyDonationsUang = {};
            const dailyDonationsBarang = {};

            // Filter dan hitung total donasi uang dan barang per hari
            donasiData.forEach(item => {
                const dateObj = new Date(item.created_at);
                if (isNaN(dateObj)) {
                    console.warn(`Invalid date format for item.created_at: ${item.created_at}. Skipping this item for chart.`);
                    return;
                }
                const date = dateObj.toISOString().split('T')[0]; // Format YYYY-MM-DD
                const qty = Number(item.qty);

                if (!isNaN(qty)) {
                    if (item.type === 'uang') {
                        dailyDonationsUang[date] = (dailyDonationsUang[date] || 0) + qty;
                    } else if (item.type === 'barang') {
                        // Untuk barang, kita hanya menjumlahkan qty tanpa memperdulikan unit
                        dailyDonationsBarang[date] = (dailyDonationsBarang[date] || 0) + qty;
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

            // Isi array kategori dan series data dengan semua tanggal dalam rentang
            while (current <= end) {
                const dateString = current.toISOString().split('T')[0];
                categories.push(dateString); // Kategori adalah tanggal
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
                    chartMonthDisplay.textContent = `pada tanggal : ${categories[0]}`;
                } else if (dateFromChart.getMonth() === lastDateFromChart.getMonth() && dateFromChart.getFullYear() === lastDateFromChart.getFullYear()) {
                    chartMonthDisplay.textContent = `pada bulan : ${monthNames[dateFromChart.getMonth()]} ${dateFromChart.getFullYear()}`;
                } else {
                    chartMonthDisplay.textContent = `periode : ${startDate} s/d ${endDate}`;
                }
            } else if (chartMonthDisplay) {
                chartMonthDisplay.textContent = `periode : Tidak ada data`;
            }

        } catch (error) {
            console.error('Error fetching data for main chart:', error);
            renderMainChart([], [], []); // Render chart dengan data kosong jika ada error
            if (chartMonthDisplay) {
                chartMonthDisplay.textContent = `periode : Error memuat data`;
            }
        }
    }

    if (token) {
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
            if (response.status === 401) {
                throw new Error('Unauthorized: Token tidak valid atau kadaluarsa. Harap login ulang.');
            }
            throw new Error('Gagal fetch data donasi: ' + response.statusText);
        }
        return response.json();
    })
    .then(result => {
        const data = result.data;
        const tbody = document.querySelector('#donasi-table tbody'); // Ini untuk tabel utama, biarkan saja jika ada tabel lain
        const donasiTerhimpunList = document.getElementById('donasi-terhimpun'); // GET THE UL ELEMENT

        if (tbody) {
            tbody.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data donasi.</td></tr>';
                // Also clear the donasi-terhimpun list if no data
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
                return;
            }

            // Render to the main table (if it exists) - this section remains largely the same
            data.forEach(item => {
            // Tentukan kelas CSS untuk warna status
            let statusColorClass = 'text-gray-900 dark:text-white'; // Default
            const currentStatus = item.status_validasi || item.status; // Ambil status yang relevan

            // Tambahkan safety check di sini
            let displayStatus = '-'; // Default display jika status tidak ada
            if (currentStatus) {
                displayStatus = currentStatus.replace(/_/g, ' ').toUpperCase();
            }

            switch (currentStatus) { // Gunakan currentStatus untuk logika warna
                case 'rejected':
                    statusColorClass = 'text-red-600 font-semibold';
                    break;
                case 'pending':
                    statusColorClass = 'text-blue-600 font-semibold';
                    break;
                case 'accepted':
                case 'success':
                    statusColorClass = 'text-green-600 font-semibold';
                    break;
            }

            const row = `
                <tr>
                    <td class="px-6 py-4">${item.id}</td>
                    <td class="px-6 py-4">${item.userid || '-'}</td>
                    <td class="px-6 py-4">${item.type || '-'}</td>
                    <td class="px-6 py-4">${item.qty || 0} ${item.unit || ''}</td>
                    <td class="px-6 py-4">${item.keterangan || '-'}</td>
                    <td class="px-6 py-4 ${statusColorClass}">
                        ${displayStatus}
                    </td>
                    <td class="px-6 py-4">${new Date(item.created_at).toLocaleString()}</td>
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
                    </td>
                </tr>
            `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            console.warn("Elemen #donasi-table tbody tidak ditemukan. Tabel donasi mungkin tidak akan ditampilkan.");
        }


        // --- RENDER DONASI TERHIMPUN (ACCEPTED STATUS ONLY) ---
        if (donasiTerhimpunList) {
            donasiTerhimpunList.innerHTML = ''; // Clear previous content

            // Menggunakan `item.status` sesuai API
            const acceptedDonations = data.filter(item => item.status === 'accepted' || item.status === 'success'); // Tambahkan 'success'

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


        // --- Hitung dan tampilkan total donasi Rupiah (untuk bagian atas) ---
        const totalDonasiRupiah = data.reduce((sum, item) => {
            // Hanya jumlahkan jika type adalah 'uang' dan qty adalah angka yang valid.
            // Hapus kondisi status_validasi === 'accepted' untuk menjumlahkan semua status.
            if (item.type === 'uang' && typeof item.qty === 'number' && !isNaN(item.qty)) {
                return sum + item.qty;
            }
            return sum;
        }, 0);

        if (totalDonasiElement) {
            totalDonasiElement.textContent = totalDonasiRupiah.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0, // Tambahkan ini jika ingin tanpa desimal
                maximumFractionDigits: 0  // Tambahkan ini jika ingin tanpa desimal
            });
        }

        // --- HITUNG DATA UNTUK RINGKASAN Uang vs Barang (ACCEPTED STATUS ONLY) ---
        let totalUang = 0;
        let totalBarang = 0;

        data.filter(item => item.status_validasi === 'accepted').forEach(item => { // Filter by accepted status
            if (item.type === 'uang') {
                totalUang += item.qty;
            } else if (item.type === 'barang') {
                totalBarang += item.qty;
            }
        });

        // Update UI text for Barang and Uang
        if (totalDonasiBarangElement) {
            totalDonasiBarangElement.textContent = totalBarang.toLocaleString('id-ID') + ' unit';
        }
        if (totalDonasiUangElement) {
            totalDonasiUangElement.textContent = totalUang.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
        }

    })
    .catch(error => {
        console.error('Error fetching all donasi data:', error);
        alert('Gagal memuat data donasi: ' + error.message);
        if (totalDonasiElement) {
            totalDonasiElement.textContent = "Error: " + error.message;
        }
        if (totalDonasiBarangElement) totalDonasiBarangElement.textContent = 'Error';
        if (totalDonasiUangElement) totalDonasiUangElement.textContent = 'Error';
    });
    // --- Fungsi Create (Insert) Donasi Baru ---
    const createDonationForm = document.getElementById('createDonationForm');
    if (createDonationForm) {
        createDonationForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData(createDonationForm);
            const data = {};
            for (let [key, value] of formData.entries()) {
                if (key === 'qty') {
                    data[key] = parseInt(value); // Pastikan qty adalah integer
                } else {
                    data[key] = value;
                }
            }

            console.log("Data yang akan dikirim ke API:", data); // Untuk debugging

            const token = localStorage.getItem('token');

            if (!token) {
                alert('Token JWT tidak ditemukan. Harap login terlebih dahulu.');
                return;
            }

            try {
                const response = await fetch('/api-proxy/donasi', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(data) // Mengirim data yang sudah bersih
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error("Detail Error dari API:", errorData); // Log error detail dari API
                    throw new Error(errorData.message || errorData.error || `Error ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();
                alert('Donasi berhasil ditambahkan!');
                console.log('Donasi berhasil:', result);

                const createProductModal = document.getElementById('createProductModal');
                if (createProductModal) {
                    // Asumsi Anda menggunakan Flowbite modal atau serupa yang menambahkan/menghapus kelas 'hidden'
                    createProductModal.classList.add('hidden');
                    createProductModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('overflow-hidden');
                    const modalBackdrop = document.querySelector('[modal-backdrop]'); // Cari dan hapus backdrop
                    if (modalBackdrop) modalBackdrop.remove();
                }

                // Setelah berhasil, muat ulang data tabel dan chart
                if (typeof window.fetchAndDisplayDonations === 'function') {
                    window.fetchAndDisplayDonations(); // Muat ulang tabel donasi
                }
                if (typeof window.fetchAndRenderMainChart === 'function') {
                    window.fetchAndRenderMainChart(); // Muat ulang chart
                }
                createDonationForm.reset(); // Reset form untuk input berikutnya
            } catch (error) {
                console.error('Error saat menambahkan donasi:', error.message);
                alert('Terjadi kesalahan: ' + error.message);
            }
        });
    }

    // --- Fungsi Edit Donasi (Global) ---
    window.editDonasi = function(id, userid, type, qty, unit, keterangan) {
        const token = localStorage.getItem("token");
        if (!token) {
            alert('Token tidak ditemukan. Harap login terlebih dahulu.');
            return;
        }

        document.getElementById('edit-donasi-id').value = id;
        document.getElementById('edit-userid').value = userid;
        document.getElementById('edit-type').value = type;
        document.getElementById('edit-qty').value = qty;
        document.getElementById('edit-unit').value = unit;
        document.getElementById('edit-keterangan').value = keterangan;

        const editModal = document.getElementById('editDonationModal');
        editModal.classList.remove('hidden');
        editModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        const backdrop = document.createElement('div');
        backdrop.setAttribute('modal-backdrop', '');
        backdrop.className = 'bg-gray-200 bg-opacity-25 fixed inset-0 z-40';
        document.body.appendChild(backdrop);

        document.querySelectorAll('[data-modal-hide="editDonationModal"]').forEach(button => {
            button.onclick = function() {
                editModal.classList.add('hidden');
                editModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                if (backdrop) backdrop.remove();
            };
        });

        const editDonationForm = document.getElementById('editDonationForm');
        editDonationForm.onsubmit = async function(event) {
            event.preventDefault();

            const donasiId = document.getElementById('edit-donasi-id').value;
            const updatedData = {
                userid: document.getElementById('edit-userid').value,
                type: document.getElementById('edit-type').value,
                qty: parseInt(document.getElementById('edit-qty').value),
                unit: document.getElementById('edit-unit').value,
                keterangan: document.getElementById('edit-keterangan').value
            };

            try {
                const response = await fetch(`/api-proxy/donasi/${donasiId}`, {
                    method: "PUT",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(updatedData)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    if (response.status === 401) {
                        throw new Error('Unauthorized: Token tidak valid atau kadaluarsa. Harap login ulang.');
                    }
                    throw new Error(errorData.message || "Gagal update data: " + response.statusText);
                }

                const result = await response.json();
                alert("Donasi berhasil diperbarui!");
                console.log("Update berhasil:", result);

                editModal.classList.add('hidden');
                editModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                if (backdrop) backdrop.remove();

                location.reload();

            } catch (err) {
                console.error("Edit error:", err);
                alert("Terjadi kesalahan saat mengedit donasi: " + err.message);
            }
        };
    };

        // --- FUNGSI BARU UNTUK UPDATE STATUS DONASI (SESUAI DOKUMENTASI validasibyadmin) ---
    window.updateDonationStatus = async function(id, newStatus) {
        const token = localStorage.getItem("token");
        if (!token) {
            alert('Token tidak ditemukan. Harap login terlebih dahulu.');
            location.reload(); 
            return;
        }

        const konfirmasi = confirm(`Apakah Anda yakin ingin mengubah status donasi ID ${id} menjadi '${newStatus}'?`);
        if (!konfirmasi) {
            location.reload();
            return;
        }

        // Tambahkan input catatan validasi
        const catatanValidasi = prompt(`Masukkan catatan untuk validasi donasi ID ${id} (opsional):`);
        if (catatanValidasi === null) { // Jika user menekan 'Cancel' pada prompt
            location.reload();
            return;
        }

        // Contoh: Ambil nama validator dari lokal storage atau default
        // Di aplikasi nyata, 'validator' ini harus diambil dari informasi user yang sedang login (misal dari token JWT)
        const validatorName = localStorage.getItem('userName') || 'Admin Sistem'; // Ganti dengan logika sebenarnya

        // Data yang akan dikirim sesuai dokumentasi API
        const requestBody = {
            id_donasi: id,
            status_validasi: newStatus,
            catatan_validasi: catatanValidasi,
            validator: validatorName
        };

        try {
            // *** PENTING: URL ini sekarang menuju ke endpoint validasi admin yang spesifik ***
            const response = await fetch(`/api-proxy/validasi-donasi/admin/validasibyadmin`, { 
                method: "PUT",
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(requestBody) 
            });

            if (!response.ok) {
                const errorData = await response.json();
                if (response.status === 401) {
                    throw new Error('Unauthorized: Token tidak valid atau kadaluarsa. Harap login ulang.');
                }
                // Pesan error akan menampilkan message dari backend jika ada
                throw new Error(errorData.message || "Gagal update status donasi: " + response.statusText);
            }

            const result = await response.json();
            alert("Status donasi berhasil diperbarui!");
            console.log("Update status berhasil:", result);

            location.reload(); 
        } catch (err) {
            console.error("Error updating donation status:", err);
            alert("Terjadi kesalahan saat memperbarui status donasi: " + err.message);
            location.reload(); 
        }
    };
    


    // --- Fungsi Hapus Donasi (Global) ---
    window.hapusDonasi = function(id) {
        const konfirmasi = confirm("Apakah kamu yakin ingin menghapus donasi ini?");
        if (!konfirmasi) return;

        const token = localStorage.getItem("token");
        if (!token) {
            alert('Token tidak ditemukan. Harap login terlebih dahulu.');
            return;
        }

        fetch(`/api-proxy/donasi/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(res => {
                if (!res.ok) {
                    if (res.status === 401) {
                        throw new Error('Unauthorized: Token tidak valid atau kadaluarsa. Harap login ulang.');
                    }
                    throw new Error("Gagal menghapus: " + res.statusText);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    alert("Donasi berhasil dihapus.");
                    location.reload();
                } else {
                    alert("Gagal menghapus donasi: " + (data.message || 'Error tidak diketahui'));
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("Terjadi kesalahan saat menghapus data: " + err.message);
            });
    };
});

