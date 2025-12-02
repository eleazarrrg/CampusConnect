<?php require __DIR__.'/../../layout_top.php'; ?>
<div class="hero">
  <div class="eyebrow">Universidad Tecnologica de Panama Â· FISC</div>
  <h1>Software Automatizado</h1>
  <p class="lede">Portada de proyecto para Mantenimiento y Pruebas, 1SF132 - II semestre. Un inicio claro, con navegaciÃ³n directa y resumen del equipo.</p>
  <p class="subline">Lic. en Ingenieria de Software Â· Proyecto: Mantenimiento y Pruebas Â· Fecha: 18-11-2025</p>
  <div class="pill-list" style="margin-top:6px">
    <span class="pill">Lic. en Ingenieria de Software</span>
    <span class="pill">Proyecto: Mantenimiento y Pruebas</span>
    <span class="pill">Fecha: 18-11-2025</span>
  </div>
  <div class="cta">
    <a class="btn solid lg" href="<?= dynamic_base() ?>/panel.php" data-test="cta-panel" onclick="alert('Vamos al panel de gestion');">Ir al panel</a>
    <a class="btn ghost lg" href="<?= dynamic_base() ?>/login.php" onclick="alert('Redirigiendo al login');">Iniciar sesion</a>
    <button class="btn outline back lg" onclick="history.back(); alert('Volviendo a la pagina anterior');">Volver</button>
  </div>
</div>

<div class="grid" style="margin-top:18px">
  <div class="card glass">
    <h3>Resumen rapido</h3>
    <ul>
      <li>Facultad de Ingenieria de Sistemas Computacionales.</li>
      <li>Curso: Mantenimiento y Pruebas.</li>
      <li>Proyecto: Software Automatizado.</li>
      <li>Seccion: 1SF132 â€“ II Semestre.</li>
      <li>Profesora: Geralis Garrido.</li>
    </ul>
    <div class="row" style="margin-top:10px; gap:8px">
      <a class="btn ghost" href="<?= dynamic_base() ?>/citas.php">Agendar cita</a>
      <a class="btn ghost" href="<?= dynamic_base() ?>/prestamos.php">Solicitar prestamo</a>
      <a class="btn ghost" href="<?= dynamic_base() ?>/eventos.php">Eventos</a>
    </div>
  </div>
  <div class="ghost-card">
    <h3>Lineas maestras</h3>
    <p class="note">Guia rapida de lo que cubre la plataforma:</p>
    <ul>
      <li>Agenda de citas con validacion de 24h y gestion de horarios.</li>
      <li>Prestamos de libros con estados y edicion segura.</li>
      <li>Publicaciones con comentarios y reacciones.</li>
      <li>Eventos filtrables y calendario con vacios informados.</li>
      <li>Autenticacion con verificacion y bloqueo por intentos.</li>
    </ul>
  </div>
</div>

<h3 style="margin-top:20px">Equipo</h3>
<div class="team-grid">
  <div class="team-card">
    <img src="<?= dynamic_base() ?>/assets/team/juan.jpg" alt="Juan Zhu" onerror="this.style.display='none';">
    <div class="avatar">JZ</div>
    <div class="meta">
      <strong>Juan Zhu</strong>
      <div class="note">8-1010-701</div>
    </div>
  </div>
  <div class="team-card">
    <img src="<?= dynamic_base() ?>/assets/team/alex.jpg" alt="Alex de Boutaud" onerror="this.style.display='none';">
    <div class="avatar">AB</div>
    <div class="meta">
      <strong>Alex de Boutaud</strong>
      <div class="note">8-1015-1644</div>
    </div>
  </div>
  <div class="team-card">
    <img src="<?= dynamic_base() ?>/assets/team/jeremy.jpg" alt="Jeremy Martinez" onerror="this.style.display='none';">
    <div class="avatar">JM</div>
    <div class="meta">
      <strong>Jeremy Martinez</strong>
      <div class="note">8-1024-1470</div>
    </div>
  </div>
  <div class="team-card">
    <img src="<?= dynamic_base() ?>/assets/team/rafael.jpg" alt="Rafael Gomez" onerror="this.style.display='none';">
    <div class="avatar">RG</div>
    <div class="meta">
      <strong>Rafael Gomez</strong>
      <div class="note">8-1011-1754</div>
    </div>
  </div>
</div>
<div class="note" style="margin-top:8px">Coloca las fotos en <code>assets/team/</code> con nombres juan.jpg, alex.jpg, jeremy.jpg, rafael.jpg para verlas en la portada.</div>
<?php require __DIR__.'/../../layout_bottom.php'; ?>

