document.addEventListener('DOMContentLoaded', function () {
  /* ---------- Sidebar (tablet / mobile) ---------- */
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('overlay');
  var menuBtn = document.getElementById('menu-toggle');

  function toggleSidebar(open) {
    if (!sidebar) return;
    sidebar.classList.toggle('open', open);
    overlay.classList.toggle('open', open);
  }
  if (menuBtn) menuBtn.addEventListener('click', function () { toggleSidebar(!sidebar.classList.contains('open')); });
  if (overlay) overlay.addEventListener('click', function () { toggleSidebar(false); });

  /* ---------- Notifikasi sukses hilang otomatis (4 detik) ---------- */
  document.querySelectorAll('[data-autohide]').forEach(function (el) {
    setTimeout(function () { el.remove(); }, 4000);
  });

  /* ---------- Modal konfirmasi untuk form ber-atribut data-confirm ---------- */
  var dialog = document.getElementById('confirm-dialog');
  var pendingForm = null;

  if (dialog) {
    var titleEl = dialog.querySelector('[data-confirm-title]');
    var textEl = dialog.querySelector('[data-confirm-text]');
    var okBtn = dialog.querySelector('[data-confirm-ok]');
    var cancelBtn = dialog.querySelector('[data-confirm-cancel]');

    document.addEventListener('submit', function (e) {
      var form = e.target;
      if (!form.matches || !form.matches('[data-confirm]')) return;
      e.preventDefault();

      pendingForm = form;
      titleEl.textContent = form.dataset.confirmTitle || 'Konfirmasi';
      textEl.textContent = form.dataset.confirm || 'Lanjutkan tindakan ini?';
      okBtn.textContent = form.dataset.confirmOk || 'Lanjutkan';
      okBtn.className = 'btn ' + (form.hasAttribute('data-confirm-danger') ? 'btn-danger' : 'btn-primary');
      dialog.showModal();
    });

    okBtn.addEventListener('click', function () {
      var form = pendingForm;
      pendingForm = null;
      dialog.close();
      if (form) form.submit();
    });
    cancelBtn.addEventListener('click', function () { pendingForm = null; dialog.close(); });
    dialog.addEventListener('click', function (e) { if (e.target === dialog) { pendingForm = null; dialog.close(); } });
  }

  /* ---------- Pilih barang: isi harga beli dan tampilkan stok ---------- */
  document.querySelectorAll('select[data-item-select]').forEach(function (select) {
    var field = select.closest('.field');
    var hint = field ? field.querySelector('[data-hint]') : null;
    var price = document.getElementById('buy_price');

    function update(overridePrice) {
      var opt = select.options[select.selectedIndex];
      if (!opt || !opt.value) { if (hint) hint.textContent = ''; return; }
      if (hint) hint.textContent = 'Stok saat ini: ' + Number(opt.dataset.stock).toLocaleString('id-ID') + ' ' + opt.dataset.unit;
      if (price && overridePrice && opt.dataset.buyPrice !== undefined) {
        price.value = parseFloat(opt.dataset.buyPrice);
      }
    }
    select.addEventListener('change', function () { update(true); });
    update(price && price.value === '');
  });

  /* ---------- Pencarian cepat pada tabel stok opname ---------- */
  var search = document.querySelector('[data-table-search]');
  if (search) {
    search.addEventListener('input', function () {
      var term = search.value.trim().toLowerCase();
      document.querySelectorAll('[data-searchable] tbody tr[data-row]').forEach(function (row) {
        row.classList.toggle('is-hidden', term !== '' && row.textContent.toLowerCase().indexOf(term) === -1);
      });
    });
  }

  /* ---------- Hitung selisih stok opname secara langsung ---------- */
  document.querySelectorAll('[data-physical]').forEach(function (input) {
    var out = input.closest('tr').querySelector('[data-diff]');
    function render() {
      if (input.value === '') { out.textContent = '-'; out.className = 'muted'; return; }
      var diff = parseInt(input.value, 10) - parseInt(input.dataset.system, 10);
      out.textContent = (diff > 0 ? '+' : '') + diff.toLocaleString('id-ID');
      out.className = diff < 0 ? 'diff-neg' : (diff > 0 ? 'diff-pos' : '');
    }
    input.addEventListener('input', render);
    render();
  });

  /* ---------- Tombol cetak ---------- */
  document.querySelectorAll('[data-print]').forEach(function (btn) {
    btn.addEventListener('click', function () { window.print(); });
  });
});
