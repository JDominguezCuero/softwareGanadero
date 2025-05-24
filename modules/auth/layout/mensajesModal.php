<!-- Modal Bootstrap -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modal-title">Mensaje Error</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p id="modal-message">Descripción</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>



<script>
function showModal(title, message, type = 'info') {
    // Usar Bootstrap Modal
    const modalEl = document.getElementById("modal");
    const titleEl = document.getElementById("modal-title");
    const messageEl = document.getElementById("modal-message");

    titleEl.textContent = title;
    messageEl.textContent = message;

    // Estilo según tipo
    titleEl.style.color = {
        'error': 'red',
        'success': 'green',
        'info': 'black'
    }[type] || 'black';

    // Crear instancia del modal con Bootstrap 4/5
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Cierre automático después de 10 segundos
    setTimeout(() => {
        modal.hide();
    }, 10000);
}
</script>
