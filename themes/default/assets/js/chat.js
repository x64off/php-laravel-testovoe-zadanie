$(document).ready(function() {
    function updateChats() {
        $.ajax({
            url: site_url+'/api/get-chat', // Путь к серверному скрипту
            type: 'POST',
            data:{
                csrf_token:csrfToken,
                get_chats:true
            },
            dataType: 'json',
            success: function(response) {
                csrfToken = response.token;
                if (response.status === 'success') {
                    var chats = response.chats;
                    var chatsContainer = $('#chat-list-ul');
                    var activeElement = $('.chat-item.active');
                    var active = null;
                    if (activeElement[0]){
                        active = activeElement[0].id;
                    }
                    
                    chatsContainer.empty();
                    $.each(chats, function(index, chat) {
                        var chatID = chat.id;
                        var chatName = chat.name;
                        var messageElement = $('<li class="chat-item" id="chat-'+chatID+'"></li>').text(chatName);
                        if(active && 'chat-'+chatID == active){
                            messageElement.addClass('active');
                            updateMessages(chatID);
                        }
                        chatsContainer.append(messageElement);
                        messageElement.on('click', function() {
                            var activeElement = $('.chat-item.active');
                            activeElement.removeClass('active');
                            $('.chat-name').text(chatName);
                            $('.chat-content').show();
                            updateMessages(chatID);
                            $('#chat-'+chatID).addClass("active");
                            $('#chat-button').on('click', function() {
                                if($('#message-input').val() != ''){
                                    var Id = $('.chat-item.active')[0].id;
                                    Id = parseInt(Id.replace('chat-', ''));
                                    sendMessage($('#message-input').val(),Id);
                                    $('#message-input').val('');
                                }
                                
                            });
                        });
                    });
                    
                }
            }
        });
    }
    function updateMessages(chat) {
        $.ajax({
            url: site_url+'/api/get-chat', // Путь к серверному скрипту
            type: 'POST',
            data:{
                csrf_token:csrfToken,
                get_messages:true,
                chat:chat
            },
            dataType: 'json',
            success: function(response) {
                csrfToken = response.token;
                if (response.status === 'success') {
                    var chatContainer = $('.chat-messages');
                    var messages = response.messages;
                    chatContainer.empty();
                    messages = messages.reverse();
                    $.each(messages, function(index, message) {
                        var messageEl = $('<div class="message" ></div>').text(message.message);
                        var timeEl = $('<div class="time"></div>').text(message.time);
                        if (user_id == message.user){
                            var authorEl = $('<div class="author"></div>').text('Вы');
                            var messageElement = $('<div class="message-box  message-box-right" id="message-'+message.id+'"></div>');
                        }
                        else{
                           
                            var authorEl = $('<div class="author"></div>').text(message.username);
                            var messageElement = $('<div class="message-box" id="message-'+message.id+'"></div>');
                        }
                            
                        messageElement.append(authorEl, messageEl, timeEl);
                        chatContainer.append(messageElement);
                
                    });
                }
            }
        });
    }
    function sendMessage(message,chat){
        $.ajax({
            url: site_url+'/api/get-chat', // Путь к серверному скрипту
            type: 'POST',
            data: {
                csrf_token:csrfToken,
                user:user_id,
                message: message,
                chat:chat
            },
            dataType: 'json',
            success: function(response) {
                csrfToken = response.token;
                if (response.status === 'success') {
                }
            }
        });
    }
    updateChats();
    setInterval(updateChats, 1000);
    const addButton = document.getElementById('addButton');
    const chatList = document.getElementById('chat-new');
    addButton.addEventListener('click', () => {
          chatList.style.display='block';
          // Создание нового li элемента
          const newItem = document.createElement('li');
          newItem.classList.add('chat-item');
          // Создание input элемента для ввода имени
          const nameInputEl = document.createElement('input');
          nameInputEl.type = 'text';
          nameInputEl.disabled = true;
          newItem.appendChild(nameInputEl);
          
          // Добавление нового элемента в chat-list-ul
          chatList.appendChild(newItem);
          nameInputEl.addEventListener('blur', () => {
            const updatedName = nameInputEl.value;
            sendPostRequest(updatedName);
            nameInputEl.disabled = true;
            chatList.style.display='none';
            $('#chat-new').empty();
          });
          // Включение редактирования после добавления элемента в список
          nameInputEl.focus();
          nameInputEl.disabled = false;
        
      });
      function sendPostRequest(name) {
        $.ajax({
            url: site_url+'/api/get-chat', // Путь к серверному скрипту
            type: 'POST',
            data: { csrf_token:csrfToken, name: name,create_chat: true },
            dataType: 'json',
            success: function(response) {
                csrfToken = response.token;
                if (response.status === 'success') {
                }
            }
        });
      }
      function handleSearchResults(results) {
        const chatId = results.chat;
        const messageId = results.message;
        var activeElement = $('.chat-item.active');
        if (activeElement) {
            activeElement.removeClass('active');
        }
        const chatElement = document.getElementById(`chat-${chatId}`);
        if (chatElement) {
            chatElement.classList.add('active');
            $('.chat-content').show();
            updateMessages(chatId);
            $('#chat-'+chatId).addClass("active");
            $('#chat-button').on('click', function() {
                if($('#message-input').val() != ''){
                    var Id = $('.chat-item.active')[0].id;
                    Id = parseInt(Id.replace('chat-', ''));
                    sendMessage($('#message-input').val(),Id);
                    $('#message-input').val('');
                }
            });
        }
        // Прокрутка к нужному сообщению
        const messageElement = document.getElementById(`message-${messageId}`);
        if (messageElement) {
          messageElement.scrollIntoView({ behavior: 'smooth' });
        }
      }
      const searchInput = document.getElementById('chat-search');
      let timeoutId; // Идентификатор таймаута
        searchInput.addEventListener('input', () => {
        clearTimeout(timeoutId); // Сброс предыдущего таймаута

        timeoutId = setTimeout(() => {
            const query = searchInput.value;
            sendAjaxRequest(query);
        }, 500); // Задержка перед отправкой запроса (500 миллисекунд)
        });
        function sendAjaxRequest(query) {
            $.ajax({
                url: site_url+'/api/get-chat', // Путь к серверному скрипту
                type: 'POST',
                data: { csrf_token:csrfToken, search: query},
                dataType: 'json',
                success: function(response) {
                    csrfToken = response.token;
                    console.log(response);
                    if (response.status === 'success') {
                        handleSearchResults(response);
                    }
                }
            });
          }
});
