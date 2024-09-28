<form action="/user/update/" method="post">
  <input id="csrf_token" type="hidden" name="csrf_token" value="{{ csrf_token }}">
  <p>
    <label for="user-id">ID:</label>
    <input id="user-id" type="number" name="id" value="{{id}}" required>
  </p>
  <p>
    <label for="user-name">Имя:</label>
    <input id="user-name" type="text" name="name" value="{{name}}" required>
  </p>
  <p>
    <label for="user-lastname">Фамилия:</label>
    <input id="user-lastname" type="text" name="lastname" value="{{lastname}}" required>
  </p>
  <p><input type="submit" value="Сохранить"></p>
</form>