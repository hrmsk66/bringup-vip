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
                <span class="help is-danger is-size-6" v-show="failed1.length !== 0">Failed to install the certificate on:</span>
                <span class="help is-danger is-size-6" v-show="failed1.length !== 0" v-text="failed1.join(', ')"></span>
            </div>
        </div>
        <div class="box">
            <h1 class="title is-4">2. Assign Chassis Number and Token</h1>
            <form method="POST" @submit.prevent="onSubmit2">
                <div class="field">
                    <label class="label">vManage IP Address</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="192.168.0.1" id="vmanageIP2" name="vmanageIP2" v-model="vmanageIP2" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Target IP Address</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="192.168.0.2, 192.168.0.3, 192.168.0.4" id="ip2" name="ip2" v-model="ip2" required>
                    </div>
                </div>

                <div class="field">
                    <div class="control">
                        <button :class="{ 'button': true, 'is-link': true, 'is-loading': loading2 } ">Submit</button>
                    </div>
                </div>
            </form>
            <div class="section">
                <span class="help is-success is-size-6" v-show="failed2.length === 0 && status2 === 200">Completed</span>
                <span class="help is-danger is-size-6" v-show="status2 === 502">Failed to fetch vedgeList.<br>Please make sure the list is loaded to vManage.</span>
                <span class="help is-danger is-size-6" v-show="failed2.length !== 0">Failed to activate:</span>
                <span class="help is-danger is-size-6" v-show="failed2.length !== 0" v-text="failed2.join(', ')"></span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                ip1: '',
                cert1: '',
                loading1: false,
                status1: '',
                failed1:[],
                vmanageIP2: '',
                ip2: '',
                loading2: false,
                status2: '',
                serial2: [],
                failed2:[]
            }
        },
        computed: {
            ips1() {
                return this.ip1.replace(' ', '').split(',')
            },
            ips2() {
                return this.ip2.replace(' ', '').split(',')
            }
        },
        methods: {
            pushCert(target) {
                return axios.post('/vedge/push', {
                    ip: target,
                    cert: this.cert1
                })
            },
            onSubmit1() {
                this.failed1 = []
                this.loading1 = true
                const promises = this.ips1.map(ip => this.pushCert(ip))

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
            fetchSerial() {
                return axios.post('/vedge/list', {
                    vmanageIP: this.vmanageIP2
                })
            },
            activateDevice(target, i) {
                console.log(target, i)
                return axios.post('/vedge/activate', {
                    ip: target,
                    uuid: this.serial2[i]['uuid'],
                    token: this.serial2[i]['token']
                })
                .then(response => {
                    const rcode1 = response.data.r1[response.data.r1.length - 1]
                    if (rcode1 === '0') {
                        console.log(`done ${response.data.target}`)
                    } else {
                        this.failed2.push(response.data.target)
                    }
                })
                .catch(error => {
                    this.failed2.push(target)
                })
            },
            onSubmit2() {
                this.failed2 = []
                this.loading2 = true
                this.fetchSerial()
                    .then(response => {
                        console.log(response)
                        if (response.data.vedgelist.length === 0) {
                            this.status2 = 502
                            throw "Failed to fetch vedgeList."
                        } 
                        this.serial2 = response.data.vedgelist
                        const promises = this.ips2.map((ip, i) => this.activateDevice(ip, i))
                        console.log(promises)
                        Promise.all(promises)
                            .then(responses => {
                                this.status2 = 200
                                this.loading2 = false
                                console.log(responses)
                            })
                            .catch(errors => {
                                this.loading2 = false
                                this.status = 501
                                console.log(errors)
                            })
                    })
                    .catch(error => {
                        this.loading2 = false
                        this.status = 502
                        console.log(error)
                    })
            }
        },
        mounted() {
          axios.post('/vedge/load')
              .then(response => {
                console.log(response)
                this.cert1 = response.data.cert
              })
              .catch(error => console.log(error))
        }
    }
</script>
