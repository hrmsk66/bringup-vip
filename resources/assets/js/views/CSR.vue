
<template>
    <div>
        <form method="POST" action="/csr" @submit.prevent="onSubmit">
        <div class="field is-horizontal">
            <div class="field-body">

        <div class="field">
        <label class="label">CSR</label> <div class="control">
            <textarea class="textarea" placeholder="Textarea" rows="20" spellcheck="false" id="csr" name="csr" v-model="csr" required></textarea>
        </div>
        </div>
        <div class="field">
        <label class="label">Signed Cert</label> <div class="control">
            <textarea class="textarea" placeholder="Textarea" rows="20" spellcheck="false" id="newcert" name="newcert" v-model="newcert" :disabled="newcert == ''"></textarea>
        </div>
        </div>

        </div>
        </div>

        <div class="field">
        <div class="control">
            <button class="button is-link">Submit</button>
        </div>
        </div>
        </form>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                csr: '',
                newcert: ''
            }
        },
        methods: {
            onSubmit() {
                let temp = {
                    csr: this.csr,
                }
                axios.post('/csr', temp)
                    .then(response => {
                        console.log(response)
                        this.newcert = response.data.newcert
                    })
                    .catch(error => console.log(error))
            }
        }
    }
</script>
