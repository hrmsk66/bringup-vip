<template>
    <div>
        <div class="box">
            <h1 class="title is-4">1. Install Self-signed Root Certificate</h1>
            <form method="POST" @submit.prevent="onSubmit1">
                <div class="field">
                    <label class="label">Target IP Address</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="192.168.0.1, 192.168.0.2, 192.168.0.3" id="ip1" name="ip1" v-model="ip1" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Root Certificate</label>
                    <div class="control">
                        <textarea class="textarea" placeholder="Textarea" rows="20" spellcheck="false" id="cert1" name="cert1" v-model="cert1" required></textarea>
                    </div>
                </div>

                <div class="field">
                    <div class="control">
                        <button :class="{ 'button': true, 'is-link': true, 'is-loading': loading1 } ">Submit</button>
                    </div>
                </div>
            </form>
            <div class="section">
                <span class="help is-success is-size-6" v-show="failed1.length === 0 && status1 === 200">Completed</span>
                <span class="help is-danger is-size-6" v-show="failed1.length !== 0">Failed to install the certificate on...</span>
                <span class="help is-danger is-size-6" v-show="failed1.length !== 0" v-text="failed1.join(', ')"></span>
            </div>
        </div>

        <div class="box">
            <h1 class="title is-4">2. Re-sync Trust Store on vManage</h1>
            <form method="POST" @submit.prevent="onSubmit2">
                <div class="field">
                    <label class="label">vManage IP Address</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="192.168.0.1" id="vmanageIP2" name="vmanageIP2" v-model="vmanageIP2" required>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button :class="{ 'button': true, 'is-link': true, 'is-loading': loading2 } ">Submit</button>
                    </div>
                </div>
            </form>
            <div class="section">
                <span class="help is-success is-size-6" v-show="syncRootCertChain2 !== 'fail' && syncRootCertChain2 !== ''" v-text="'syncRootCertChain: ' + syncRootCertChain2"></span>
                <span class="help is-danger is-size-6" v-show="syncRootCertChain2 === 'fail'">Failed. <br>Please make sure the vManage web console is ready.</span>
            </div>
        </div>

        <div class="box">
            <h1 class="title is-4">3. Generate Certificate</h1>
            <form method="POST" @submit.prevent="onSubmit3">
                <div class="field is-horizontal">
                    <div class="field-body">
                        <div class="field">
                            <label class="label">CSR</label> <div class="control">
                                <textarea class="textarea" placeholder="Textarea" rows="20" spellcheck="false" id="csr3" name="csr3" v-model="csr3" required></textarea>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Signed Cert</label> <div class="control">
                                <textarea class="textarea" placeholder="Textarea" rows="20" spellcheck="false" id="newcert3" name="newcert3" v-model="newcert3" :disabled="newcert3 == ''"></textarea>
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

    </div>
</template>

<script>
    export default {
        data() {
            return {
                ip1: '192.168.0.1, 192.168.0.2, 192.168.0.3',
                cert1: '',
                loading1: false,
                status1: '',
                failed1:[],
                vmanageIP2:'',
                loading2: false,
                syncRootCertChain2:'',
                csr3: '',
                newcert3: ''
            }
        },
        computed: {
            ips1() {
                return this.ip1.replace(' ', '').split(',').map(x => x.split(':'))
            },
            vmanageIPPort() {
                return this.vmanageIP2.split(':')
            }
        },
        methods: {
            pushCert(ip, port='22') {
                return axios.post('/controllers/push', {
                    ip,
                    port,
                    cert: this.cert1
                })
            },
            onSubmit1() {
                this.failed1 = []
                this.status1 = ''
                this.loading1 = true
                const promises = this.ips1.map(x => {
                    return x.length === 2 ? this.pushCert(x[0], x[1]) : this.pushCert(x[0])
                })
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
                                this.failed1.push(response.data.target)
                            }
                        })
                        this.status1 = 200
                        this.loading1 = false
                        console.log(responses)
                    })
                    .catch(error => {
                        this.loading1 = false
                        console.log(error)
                    })
            },
            onSubmit2() {
                this.loading2 = true
                const vmanageIP = this.vmanageIPPort[0]
                const vmanagePort = this.vmanageIPPort.length === 2 ? this.vmanageIPPort[1] : '8443'
                axios.post('/controllers/resync', {
                    vmanageIP,
                    vmanagePort
                })
                .then(response => {
                    console.log(response)
                    this.loading2 = false
                    if (response.data.r1) {
                        this.syncRootCertChain2 = response.data.r1.syncRootCertChain
                    } else {
                        this.syncRootCertChain2 = 'fail'
                    }
                })
                .catch(error => {
                    this.loading2 = false
                    console.log(error)
                })
            },
            onSubmit3() {
                let temp = {
                    csr: this.csr3,
                }
                axios.post('/controllers/csr', temp)
                    .then(response => {
                        console.log(response)
                        this.newcert3 = response.data.newcert
                    })
                    .catch(error => console.log(error))
            }
        },
        mounted() {
          axios.post('/controllers/load')
              .then(response => {
                console.log(response)
                this.cert1 = response.data.cert
              })
              .catch(error => console.log(error))
        }
    }
</script>
