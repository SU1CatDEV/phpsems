{% if not user_authorized %}
    <p><a href="/user/auth/">Вход в систему</a></p>
{% else %}
    <p>Добро пожаловать на сайт, {{ user_name }}!</p>
    <p><a href="/user/logout/">Выйти</a></p>
{% endif %}