<form action="/user/delete/" method="post">
  <input id="csrf_token" type="hidden" name="csrf_token" value="{{ csrf_token }}">
  <p>
    <label for="user-id">ID:</label>
    <input id="user-id" type="number" name="id" value="{{id}}" required>
  </p>
  <p><input type="submit" value="Сохранить"></p>
</form>