<template>
  <div class="chat-container">
    <h2>Mentor de prácticas</h2>

    <div class="chat-box">
      <div 
        v-for="(msg, index) in messages" 
        :key="index"
        :class="msg.role === 'Alumno' ? 'msg user' : 'msg bot'"
      >
        <strong>{{ msg.role }}:</strong> {{ msg.text }}
      </div>
    </div>

    <div class="input-area">
      <input 
        v-model="input"
        placeholder="Escribe tu pregunta..."
        @keyup.enter="sendMessage"
      />
      <button @click="sendMessage">Enviar</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"

const input = ref("")
const messages = ref([])

async function sendMessage() {

  if (!input.value) return

  messages.value.push({
    role: "Alumno",
    text: input.value
  })

  const question = input.value
  input.value = ""

  try {

    const res = await fetch("http://localhost:8000/api/mentor", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        message: question
      })
    })

    const data = await res.json()

    messages.value.push({
      role: "Mentor",
      text: data.choices[0].message.content
    })

  } catch (error) {

    messages.value.push({
      role: "Mentor",
      text: "Error al conectar con el mentor."
    })

  }

}
</script>

<style>
.chat-container{
  max-width:600px;
  margin:auto;
  font-family:Arial;
}

.chat-box{
  border:1px solid #ddd;
  height:300px;
  overflow-y:auto;
  padding:10px;
  margin-bottom:10px;
}

.msg{
  margin-bottom:10px;
}

.user{
  text-align:right;
}

.bot{
  text-align:left;
}

.input-area{
  display:flex;
  gap:10px;
}

input{
  flex:1;
  padding:8px;
}

button{
  padding:8px 12px;
}
</style>