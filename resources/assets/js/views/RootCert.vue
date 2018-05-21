<template>
    <div>
        <form method="POST" action="/rootcert" @submit.prevent="onSubmit">
        <div class="field">
        <label class="label">Target IP Address</label>
        <div class="control">
            <input class="input" type="text" placeholder="192.168.0.1, 192.168.0.2, 192.168.0.3" id="ip" name="ip" v-model="ip" required>
        </div>
        </div>

        <div class="field">
        <label class="label">Root Certificate</label>
        <div class="control">
            <textarea class="textarea" placeholder="Textarea" rows="20" spellcheck="false" id="cert" name="cert" v-model="cert" required></textarea>
        </div>
        </div>

        <div class="field">
        <div class="control">
            <button :class="{ 'button': true, 'is-link': true, 'is-loading': loading } ">Submit</button>
        </div>
        </div>
        </form>
        <div class="section">
            <span class="help is-danger is-size-6" v-show="failed.length !== 0">Failed to install the certificate on...</span>
            <span class="help is-danger is-size-6" v-show="failed.length !== 0" v-text="failed.join(', ')"></span>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                'ip': '',
                'cert': '',
                'loading': false,
                'failed':[]
            }
        },
        computed: {
            ips() {
                return this.ip.replace(' ', '').split(',')
            }
        },
        methods: {
            pushCert(target) {
                return axios.post('/rootcert', {
                    ip: target,
                    cert: this.cert
                })
            },
            onSubmit() {
                this.failed = []
                this.loading = true
                const promises = this.ips.map(ip => this.pushCert(ip))

                Promise.all(promises)
                    .then(responses => {
                        responses.forEach(response => {
                            const r1 = response.data.r1
                            const r2 = response.data.r1
                            const rcode1 = response.data.r1[response.data.r1.length - 1]
                            const rcode2 = response.data.r2[response.data.r2.length - 1]
                            if (rcode1 === '0' && rcode2 === '0') {
                                console.log(`done ${response.data.target}`)
                            } else {
                                this.failed.push(response.data.target)
                            }
                        })
                        this.loading = false
                        console.log(responses)
                        alert('Finished')
                    })
                    .catch(error => {
                        this.loading = false
                        console.log(error)
                    })
            }
        },
        mounted() {
          axios.post('/rootcert/load')
              .then(response => {
                console.log(response)
                this.cert = response.data.cert
              })
              .catch(error => console.log(error))
        }
    }
</script>
