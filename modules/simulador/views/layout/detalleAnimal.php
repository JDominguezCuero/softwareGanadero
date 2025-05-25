<div id="modalAnimal" class="modal-overlay" style="display: none; /* ESTO ES CLAVE */ position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
    <div class="modal-animal-content" style="width: 90%; max-width: 1000px; background: white; border-radius: 12px; padding: 20px; position: relative; overflow: auto; max-height: 95vh;">

        <span class="cerrar-modal" onclick="cerrarModal()" style="position: absolute; top: 20px; right: 30px; font-size: 35px; cursor: pointer;">&times;</span>

        <div id="escenarioModal" style="background-image: url('../../modules/simulador/images/escenario-establo.png'); background-size: cover; padding: 20px; border-radius: 12px; display: flex; flex-direction: column; gap: 20px;">

            <div class="selector-escenario" style="text-align: center;">
                <button onclick="cambiarFondo('granja')">🌾 Granja</button>
                <button onclick="cambiarFondo('establo')">🏠 Establo</button>
                <button onclick="cambiarFondo('noche')">🌙 Noche</button>
            </div>

            <h2 id="modalNombre" style="text-align: center; margin-bottom: 15px; font-weight: bold; color: white;"></h2>

            <div class="modelo-3d-container"  style="width: 100%; height: 400px; border-radius: 8px; padding: 10px;">
                <model-viewer 
                    id="modelo3DAnimal" 
                    src=""  alt="Modelo 3D Animal" 
                    auto-rotate
                    camera-controls 
                    interaction-prompt="none" 
                    animation-name="Idle" 
                    autoplay 
                    style="width: 100%; height: 100%;">
                </model-viewer>
            </div>

            <div class="estado">
                <div class="barra-progreso">
                    <label>🍽️</label>
                    <div class="barra">
                        <div class="progreso verde" id="barra-modal-alimentacion" style="width: <?= $animal['alimentacion'] ?>%"><?= $animal['alimentacion'] ?>%</div>
                    </div>
                </div>

                <div class="barra-progreso">
                    <label>🚿</label>
                    <div class="barra">
                        <div class="progreso azul" id="barra-modal-higiene" style="width: <?= $animal['higiene'] ?>%"><?= $animal['higiene'] ?>%</div>
                    </div>
                </div>

                <div class="barra-progreso">
                    <label>💊</label>
                    <div class="barra">
                        <div class="progreso rojo" id="barra-modal-salud" style="width: <?= $animal['salud'] ?>%"><?= $animal['salud'] ?>%</div>
                    </div>
                </div>
            </div>

            <div class="acciones-avanzadas" style="text-align: center; display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
                <button onclick="accionModal('alimentar')">🍽️ Alimentar</button>
                <button onclick="accionModal('bañar')">🚿 Bañar</button>
                <button onclick="accionModal('medicar')">💊 Medicar</button>
                <button onclick="accionModal('dormir')">😴 Dormir</button>
                <button onclick="accionModal('jugar')">🎾 Jugar</button>
                <button class="btn-accion btn-historial" onclick="verHistorial()">Ver Historial</button>
            </div>


        </div>
    </div>
</div>