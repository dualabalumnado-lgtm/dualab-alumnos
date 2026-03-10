<template>

  <div class="chat-wrapper">
  
    <div class="chat-header">
      Mentor de prácticas 🌱
  
    </div>
  
    <div class="chat-box" ref="chatBox">
  
      <div
        v-for="(msg,index) in messages"
        :key="index"
        :class="msg.role === 'Alumno' ? 'msg user' : 'msg bot'"
      >
        {{ msg.text }}
      </div>
  
    </div>
  
    <div class="chat-input">
      <input
        v-model="input"
        placeholder="Escribe tu pregunta..."
        @keyup.enter="sendMessage"
      />
      <button @click="sendMessage">
        Enviar
      </button>
    </div>
  
  </div>
  
  </template>
  
  <script setup>
  
  import { ref, nextTick, onMounted } from "vue"
  
  const input = ref("")
  const messages = ref([])
  const chatBox = ref(null)
  
  onMounted(() => {
  
    messages.value.push({
      role:"Mentor",
      text:"Hola 👋 Soy el mentor virtual. ¿En qué puedo ayudarte con tus prácticas?"
    })
  
  })
  
  async function sendMessage(){
  
    if(!input.value) return
  
    messages.value.push({
      role:"Alumno",
      text:input.value
    })
  
    const question = input.value
    input.value=""
  
    try{
  
      const res = await fetch("http://localhost:8000/api/mentor",{
        method:"POST",
        headers:{
          "Content-Type":"application/json"
        },
        body:JSON.stringify({
          message:question
        })
      })
  
      const data = await res.json()
  
      messages.value.push({
        role:"Mentor",
        text:data.choices?.[0]?.message?.content || data.message || "Sin respuesta"
      })
  
    }catch{
  
      messages.value.push({
        role:"Mentor",
        text:"El mentor no está disponible ahora."
      })
  
    }
  
    await nextTick()
  
    chatBox.value.scrollTop = chatBox.value.scrollHeight
  
  }
  
  </script>
  
  <style>
  
  .chat-wrapper{
    position:fixed;
    bottom:30px;
    right:30px;
    width:420px;
    height:520px;
    background:white;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
    display:flex;
    flex-direction:column;
    font-family:Arial;
  }
  
  .chat-header{
    background:#16a34a;
    color:white;
    padding:14px;
    font-weight:bold;
    border-radius:12px 12px 0 0;
  }
  
  .chat-box{
    flex:1;
    overflow-y:auto;
    padding:12px;
    background:#f0fdf4;
    display:flex;
    flex-direction:column;
  }
  
  .msg{
    padding:10px 14px;
    border-radius:15px;
    margin-bottom:10px;
    max-width:75%;
    font-size:14px;
  }
  
  .user{
    background:#16a34a;
    color:white;
    align-self:flex-end;
    margin-left:auto;
  }
  
  .bot{
    background:white;
    border:1px solid #ddd;
  }
  
  .chat-input{
    display:flex;
    border-top:1px solid #ddd;
  }
  
  .chat-input input{
    flex:1;
    border:none;
    padding:12px;
    outline:none;
  }
  
  .chat-input button{
    border:none;
    background:#16a34a;
    color:white;
    padding:12px 18px;
    cursor:pointer;
    font-weight:bold;
  }
  
  .chat-input button:hover{
    background:#15803d;
  }
  
  </style>