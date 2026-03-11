<template>

  <div class="chat-wrapper">
  
    <div class="chat-header">

<img src="/src/assets/logo.png" class="chat-logo"/>

<span>Mentor de prácticas</span>

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
  const suggestions = [
  "¿Cuál es el horario de prácticas?",
  "¿Quién es mi tutor?",
  "¿Cuántas horas debo hacer?",
  "¿Cómo justifico una ausencia?"
]


  
  onMounted(() => {
  
    messages.value.push({
      role:"Mentor",
      text:"Hola 👋 Soy el mentor virtual. ¿En qué puedo ayudarte?"
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
  width:430px;
  height:520px;
  background:#000000;
  border-radius:14px;
  box-shadow:0 12px 35px rgba(0,0,0,0.35);
  display:flex;
  flex-direction:column;
  font-family:Arial;
  overflow:hidden;
}

/* HEADER CON GRADIENTE DEL LOGO */

.chat-header{
  background: linear-gradient(
    160deg,
    #1F6935 0%,
    #59BF38 45%,
    #AEE565 80%
  );
  color:white;
  padding:14px;
  font-weight:bold;
  display:flex;
  align-items:center;
  gap:10px;
}

.chat-logo{
  width:34px;
  height:34px;
  object-fit:contain;
}

/* ÁREA MENSAJES */

.chat-box{
  flex:1;
  overflow-y:auto;
  padding:14px;
  background:#C6D8C6;
  display:flex;
  flex-direction:column;
}

/* BURBUJAS */

.msg{
  padding:10px 14px;
  border-radius:16px;
  margin-bottom:10px;
  max-width:75%;
  font-size:14px;
}

/* MENSAJE DEL ALUMNO */

.user{
  background:#59BF38;
  color:white;
  align-self:flex-end;
  margin-left:auto;
}

/* MENSAJE DEL MENTOR */

.bot{
  background:white;
  border:1px solid #AEE565;
  color:#1F6935;
}

/* INPUT */

.chat-input{
  display:flex;
  background:#000000;
  border-top:1px solid #1F6935;
}

.chat-input input{
  flex:1;
  border:none;
  padding:12px;
  outline:none;
  background:#000000;
  color:white;
}

.chat-input input::placeholder{
  color:#C6D8C6;
}

/* BOTÓN */

.chat-input button{
  border:none;
  background:#59BF38;
  color:white;
  padding:12px 18px;
  cursor:pointer;
  font-weight:bold;
}

.chat-input button:hover{
  background:#1F6935;
}
  
  </style>