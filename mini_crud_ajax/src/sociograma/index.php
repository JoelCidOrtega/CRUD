<?php
include 'includes/header.php';
include 'includes/function.php';

$old_field = $old_field ?? [];
$errors = $errors ?? [];
?>
<h1>Sociograma: Nueva Clase</h1>
<form action="process.php" method="post">

  <!-- 1. Tu nombre -->
  <label>1. Tu nombre:</label>test
  <input type="text" name="nombre">
  <?= field_error('nombre', $errors) ?>
  <p></p>

  <!-- 2. ¿Qué tanto te gusta trabajar con tus compañeros? -->
  <label>2. ¿Qué tanto te gusta trabajar con tus compañeros? (1-5)</label>
  <input type="number" name="gusto" min="1" max="5" required>
  <?= field_error('gusto', $errors) ?>
  <p></p>

  <!-- 3. Fecha de hoy -->
  <label>3. Fecha de hoy:</label>
  <input type="date" name="fecha" required>
  <?= field_error('fecha', $errors) ?>
  <p></p>

  <!-- 4. Hora -->
  <label>4. Hora en que estás completando el formulario:</label>
  <input type="time" name="hora" value="<?= old_field('hora', $old_field) ?>" required>
  <?= field_error('hora', $errors) ?>
  <p></p>

  <!-- 5. Preferencia de trabajo -->
  <label>5. ¿Prefieres trabajar solo o en grupo?</label>
  <input type="radio" name="preferencia" value="solo" <?= old_field('preferencia', $old_field) === 'solo' ? 'checked' : '' ?> required> Solo
  <input type="radio" name="preferencia" value="grupo" <?= old_field('preferencia', $old_field) === 'grupo' ? 'checked' : '' ?>> En grupo
  <?= field_error('preferencia', $errors) ?>
  <p></p>

  <!-- 6. ¿Con quién te gustaría trabajar? -->
  <label>6. ¿Con quién te gustaría trabajar? (marca todos los que quieras)</label>
  <input type="checkbox" name="compa[]" value="sandro" <?= in_array('sandro', $old_field['compa'] ?? []) ? 'checked' : '' ?>> Sandro
  <input type="checkbox" name="compa[]" value="jan" <?= in_array('jan', $old_field['compa'] ?? []) ? 'checked' : '' ?>>
  Jan
  <input type="checkbox" name="compa[]" value="elia" <?= in_array('elia', $old_field['compa'] ?? []) ? 'checked' : '' ?>>
  Elia
  <?= field_error('compa', $errors) ?>
  <p></p>

  <!-- 7. ¿Con quién no te gustaría trabajar? -->
  <label>7. ¿Con quién no te gustaría trabajar? (marca todos los que quieras)</label>
  <input type="checkbox" name="neg[]" value="sandro" <?= in_array('sandro', $old_field['neg'] ?? []) ? 'checked' : '' ?>>
  Sandro
  <input type="checkbox" name="neg[]" value="jan" <?= in_array('jan', $old_field['neg'] ?? []) ? 'checked' : '' ?>> Jan
  <input type="checkbox" name="neg[]" value="elia" <?= in_array('elia', $old_field['neg'] ?? []) ? 'checked' : '' ?>>
  Elia
  <?= field_error('neg', $errors) ?>
  <p></p>

  <!-- 8. Motivo -->
  <label>8. Explica el motivo:</label>
  <textarea name="motivo" rows="4" required><?= old_field('motivo', $old_field) ?></textarea>
  <?= field_error('motivo', $errors) ?>
  <p></p>

  <!-- 9. ¿A quién pedirías ayuda? -->
  <label>9. ¿A quién pedirías ayuda si no entiendes algo?</label>
  <select name="ayuda" required>
    <option value="">Selecciona una persona</option>
    <option value="sandro" <?= old_field('ayuda', $old_field) === 'sandro' ? 'selected' : '' ?>>Sandro</option>
    <option value="jan" <?= old_field('ayuda', $old_field) === 'jan' ? 'selected' : '' ?>>Jan</option>
    <option value="elia" <?= old_field('ayuda', $old_field) === 'elia' ? 'selected' : '' ?>>Elia</option>
  </select>
  <?= field_error('ayuda', $errors) ?>
  <p></p>

  <!-- 10. Color -->
  <label>10. Elige un color que represente cómo te sientes en la clase:</label>
  <input type="color" name="color" value="<?= old_field('color', $old_field) ?>" required>
  <?= field_error('color', $errors) ?>
  <p></p>

  <!-- 11. Comentarios -->
  <label>11. Explica brevemente cómo te has sentido en esta nueva clase:</label>
  <textarea name="comentarios" rows="4" required><?= old_field('comentarios', $old_field) ?></textarea>
  <?= field_error('comentarios', $errors) ?>
  <p></p>

  <!-- 12. Lenguajes de programación -->
  <label>12. ¿Qué lenguajes de programación te gusta usar?</label>
  <?php
  $langs = ['javascript', 'python', 'java', 'php', 'csharp', 'cpp'];
  foreach ($langs as $lang) {
    $checked = in_array($lang, $old_field['lenguajes'] ?? []) ? 'checked' : '';
    echo "<input type='checkbox' name='lenguajes[]' value='$lang' $checked> " . ucfirst($lang);
  }
  ?>
  <?= field_error('lenguajes', $errors) ?>
  <p></p>

  <!-- 13. Stack -->
  <label>13. ¿Eres más de Frontend o Backend?</label>
  <input type="radio" name="stack" value="frontend" <?= old_field('stack', $old_field) === 'frontend' ? 'checked' : '' ?>
    required> Frontend
  <input type="radio" name="stack" value="backend" <?= old_field('stack', $old_field) === 'backend' ? 'checked' : '' ?>>
  Backend
  <input type="radio" name="stack" value="fullstack" <?= old_field('stack', $old_field) === 'fullstack' ? 'checked' : '' ?>> Fullstack
  <?= field_error('stack', $errors) ?>
  <p></p>

  <!-- 14. Rol -->
  <label>14. ¿Qué rol prefieres en un equipo?</label>
  <?php
  $roles = ['lider', 'programador', 'diseñador', 'tester'];
  foreach ($roles as $rol) {
    $checked = in_array($rol, $old_field['roles'] ?? []) ? 'checked' : '';
    echo "<input type='checkbox' name='roles[]' value='$rol' $checked> " . ucfirst($rol);
  }
  ?>
  <?= field_error('roles', $errors) ?>
  <p></p>

  <!-- 15. Disponibilidad -->
  <label>15. Disponibilidad horaria para trabajar en grupo</label>
  <?php
  $horas = ['mañana', 'tarde', 'noche'];
  foreach ($horas as $hora) {
    $checked = in_array($hora, $old_field['disponibilidad'] ?? []) ? 'checked' : '';
    echo "<input type='checkbox' name='disponibilidad[]' value='$hora' $checked> " . ucfirst($hora);
  }
  ?>
  <?= field_error('disponibilidad', $errors) ?>
  <p></p>

  <!-- 16. Observaciones -->
  <label>16. Observaciones adicionales:</label>
  <textarea name="observaciones" rows="4" required><?= old_field('observaciones', $old_field) ?></textarea>
  <?= field_error('observaciones', $errors) ?>
  <p></p>

  <!-- 17. Nivel -->
  <label>17. Nivel de experiencia en programación:</label>
  <select name="nivel" required>
    <option value="">Selecciona nivel</option>
    <?php
    $niveles = ['principiante', 'intermedio', 'avanzado', 'experto'];
    foreach ($niveles as $nivel) {
      $selected = old_field('nivel', $old_field) === $nivel ? 'selected' : '';
      echo "<option value='$nivel' $selected>" . ucfirst($nivel) . "</option>";
    }
    ?>
  </select>
  <?= field_error('nivel', $errors) ?>
  <p></p>

  <!-- 18. IDE -->
  <label>18. Editor / IDE que usas habitualmente:</label>
  <select name="ide" required>
    <option value="">Selecciona editor/IDE</option>
    <?php
    $ides = ['vscode', 'phpstorm', 'sublime', 'vim', 'otro'];
    foreach ($ides as $ide) {
      $selected = old_field('ide', $old_field) === $ide ? 'selected' : '';
      echo "<option value='$ide' $selected>" . ucfirst($ide) . "</option>";
    }
    ?>
  </select>
  <?= field_error('ide', $errors) ?>
  <p></p>

  <!-- 19. Git -->
  <label>19. ¿Usas control de versiones (Git)?</label>
  <input type="radio" name="git" value="si" <?= old_field('git', $old_field) === 'si' ? 'checked' : '' ?> required> Sí
  <input type="radio" name="git" value="no" <?= old_field('git', $old_field) === 'no' ? 'checked' : '' ?>> No
  <?= field_error('git', $errors) ?>
  <p></p>

  <!-- 20. Preferencias o restricciones de comunicación -->
  <label>20. Preferencias o restricciones de comunicación (mensajería, reuniones, etc.):</label>
  <textarea name="comunicacion" rows="3"
    placeholder="Por ejemplo: prefiero mensajes, evitar llamadas por la mañana, etc."
    required><?= old_field('comunicacion', $old_field) ?></textarea>
  <?= field_error('comunicacion', $errors) ?>
  <p></p>
  <input type="submit" value="enviar" />
</form>
<?php
include 'includes/footer.php';
?>