<template>

    <div class="container">
        <notifications group="notifications"/>

        <div class="content container-fluid bootstrap snippets bootdey">
            <div class="row row-broken">
                <div class="col-sm-3 col-xs-12">
                    <div class="col-inside-lg decor-default chat" style="overflow: scroll; outline: none;"
                         tabindex="5000">
                        <div class="chat-users">
                            <h6>Users</h6>
                            <div class="user" :class="active_chat == null?'active-chat' :''"
                                 v-on:click="openGeneralChat">
                                <div class="avatar">
                                    <img
                                        src="https://img.favpng.com/18/5/20/blue-human-behavior-silhouette-area-communication-png-favpng-wLT3QYknSwc68uu9GAUHGS5FY_t.jpg"
                                        alt="User name">
                                    <div class="status online"></div>
                                </div>
                                <div class="name">General Chat Room</div>
                                <div class="mood" style="height: 20px"></div>
                            </div>

                            <div class="user" :class="active_chat == 'private-'+user.id?'active-chat' :''"
                                 v-for="user in users" v-on:click="openPrivateChat(user)">
                                <div class="avatar">
                                    <img :src="user.avatar" alt="User name">
                                    <div class="status online"></div>
                                </div>
                                <div class="name">{{user.name}} ({{user.username}})</div>
                                <div class="mood" style="height: 20px"></div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-sm-9 col-xs-12 chat" id="chat-body" style="overflow: scroll; outline: none;"
                     v-chat-scroll @v-chat-scroll-top-reached="loadMoreMessages" tabindex="5001">
                    <div class="col-inside-lg decor-default">
                        <div class="chat-body">
                            <h6>Chat</h6>

                            <div class="answer " v-bind:class="message.sender.id == currentUser.id ? 'right' : 'left'"
                                 v-for="message in open_chat_messages">
                                <div class="avatar">
                                    <img :src="currentUser.avatar" :alt="currentUser.name">
                                </div>
                                <div class="name">{{message.sender.name}}</div>
                                <div class="text">
                                    {{message.content}}
                                </div>
                                <div class="time">{{message.human_date}}</div>
                            </div>

                            <div class="answer-add">
                                <textarea v-model="message" rows="4" placeholder="Write a message"></textarea>
                                <span class="answer-btn answer-btn-1"></span>
                                <span class="answer-btn answer-btn-2"></span>
                                <button class="btn btn-lg btn-success" v-on:click="sendMessage">send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {

        data() {
            return {
                message: null,
                open_chat_messages: Array,
                open_chat_next_page: null,
                users: Array,
                active_chat: null,
            };
        },
        props: {
            currentUser: {
                type: Object,
                required: true
            }
        },
        methods: {
            renderGeneralChat() {
                axios.get('chat/general').then(response => {
                    this.open_chat_messages = response.data.messages.data.reverse();
                    this.open_chat_next_page = response.data.messages.next_page_url;
                    this.active_chat = null;
                });
            },
            loadMoreMessages() {
                if (this.open_chat_next_page != null) {
                    axios.get(this.open_chat_next_page).then(response => {
                        this.open_chat_messages = [...response.data.messages.data.reverse(), ...this.open_chat_messages];
                        this.open_chat_next_page = response.data.messages.next_page_url;

                    });
                }

            },
            sendMessage() {
                var send_url = (this.active_chat == null) ? 'chat/general/send' : 'chat/private/' + this.active_chat.split('-')[1] + '/send';
                if (this.message != "" && this.message != null) {
                    axios.post(send_url, {content: this.message}).then(response => {
                        if (response.status != 201) {
                            this.$notify({
                                group: 'notifications',
                                type: "error",
                                title: "Couldn't deliver the message",
                            });
                        } else {
                            this.open_chat_messages.push({
                                content: this.message,
                                human_date: "2 seconds ago",
                                sender: this.currentUser
                            });
                            this.message = null;

                        }
                    }).catch(error => {
                        var errors = ""
                        $.each(error.response.data.errors, function (key, val) {
                            errors += key + " : " + val + "<br>";
                        });
                        this.$notify({
                            group: 'notifications',
                            type: "error",
                            title: error.response.data.message,
                            text: errors
                        });
                    });

                }


            },
            openGeneralChat() {
                this.renderGeneralChat();
                Echo.join('general.chat').listen('GeneralMessageSent', event => {
                    if (this.active_chat == null) {
                        this.open_chat_messages.push(event.message);
                    } else {
                        this.$notify({
                            group: 'notifications',
                            title: "general chat (" + event.message.sender.name + ")",
                            text: event.message.content
                        });
                    }
                }).here(users => {
                    this.users = users;
                })
                    .joining(user => {
                        this.users.push(user);
                    })
                    .leaving(user => {
                        this.users = this.users.filter(u => u.id != user.id);

                    })
                ;
            },
            renderPrivateChat(user) {
                axios.get('chat/private/' + user.id).then(response => {
                    this.open_chat_messages = response.data.messages.data.reverse();
                    this.open_chat_next_page = response.data.messages.next_page_url;
                    this.active_chat = 'private-' + user.id;
                });
            },
            openPrivateChat(user) {
                axios.get('chat/private/' + user.id).then(response => {
                    this.open_chat_messages = response.data.messages.data.reverse();
                    this.open_chat_next_page = response.data.messages.next_page_url;
                    this.active_chat = 'private-' + user.id;
                });
            },
            listenToPrivateChat() {
                Echo.private('private.chat.' + this.currentUser.id).listen('PrivateMessageSent', event => {
                    if (this.active_chat == 'private-' + event.message.sender_id) {
                        this.open_chat_messages.push(event.message);
                    } else {
                        this.$notify({
                            group: 'notifications',
                            title: "private chat (" + event.message.sender.name + ")",
                            type: "success",
                            text: event.message.content
                        });
                    }
                })
            }

        },
        mounted() {
            this.openGeneralChat();
            this.listenToPrivateChat();

        }
    }
</script>
