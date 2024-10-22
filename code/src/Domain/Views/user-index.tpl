<p>Список пользователей в хранилище</p>

<ul id="navigation">
    {% for user in users %}
        <li id="user-{{ user.getUserId() }}">
        {{ user.getUserName() }} {{ user.getUserLastName() }}. 
        День рождения: {{ user.getUserBirthday() | date('d.m.Y') }}
        <a href='/manage/update?id={{user.getUserId()}}&name={{user.getUserName()}}&lastname={{user.getUserLastName()}}'>Обновить</a>
        <a href="#" onclick="deleteUser({{ user.getUserId() }})">Удалить</a>
        </li>
    {% endfor %}
</ul>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        // Perform AJAX request to delete the user
        fetch(`/user/delete?id=${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${userId}`
        })
        .then(response => {
            document.getElementById(`user-${userId}`).remove();
        })
        .catch(error => console.error(error));
    }
}
</script>

