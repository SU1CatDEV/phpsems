<p>Список пользователей в хранилище</p>

<ul id="navigation">
    {% for user in users %}
        <li>
        {{ user.getUserName() }} {{ user.getUserLastName() }}. 
        День рождения: {{ user.getUserBirthday() | date('d.m.Y') }}
        <a href='/manage/update?id={{user.getUserId()}}&name={{user.getUserName()}}&lastname={{user.getUserLastName()}}'>Обновить</a></li>
    {% endfor %}
</ul>