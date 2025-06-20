<section class="bg-gray-50 dark:bg-gray-800 py-12">
    <div class="max-w-screen-xl px-4 mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            <!-- Kiri: Info Kontak -->
            <div class="text-gray-800 dark:text-gray-100 space-y-6">
                <h2 class="text-3xl font-extrabold mb-4">Hubungi Kami</h2>
                <p class="text-gray-600 dark:text-gray-300">
                    Punya pertanyaan seputar donasi atau ingin berdiskusi lebih lanjut? Kami siap membantu.
                </p>
                <div>
                    <h3 class="text-lg font-semibold">Alamat</h3>
                    <p>Jl. Contoh No. 123, Jakarta, Indonesia</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Nomor Telepon</h3>
                    <p>+62 812-3456-7890</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Email</h3>
                    <p>donasi@contoh.org</p>
                </div>
            </div>

            <!-- Kanan: Form Kontak -->
            <form action="https://formspree.io/f/yourformid" method="POST"
                class="space-y-6 bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                <div>
                    <label for="name"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama</label>
                    <input type="text" name="name" id="name" required
                        class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-800 dark:text-white"
                        placeholder="Nama lengkap Anda" />
                </div>
                <div>
                    <label for="email"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input type="email" name="_replyto" id="email" required
                        class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-800 dark:text-white"
                        placeholder="nama@email.com" />
                </div>
                <div>
                    <label for="message"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pesan</label>
                    <textarea name="message" id="message" rows="5" required
                        class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-800 dark:text-white"
                        placeholder="Tulis pesan Anda di sini..."></textarea>
                </div>
                <button type="submit"
                    class="bg-purple-700 hover:bg-purple-800 text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-900">
                    Kirim Pesan
                </button>
            </form>

        </div>
    </div>
</section>
