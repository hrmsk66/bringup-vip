<template>
    <div>
        <form method="POST" action="/rootcert" @submit.prevent="onSubmit">
        <div class="field">
        <label class="label">vManage IP Address</label>
        <div class="control">
            <input class="input" type="text" placeholder="192.168.0.1" id="vmanageIP" name="vmanageIP" v-model="vmanageIP" required>
        </div>
        </div>
        <div class="field">
        <label class="label">Target IP Address</label>
        <div class="control">
            <input class="input" type="text" placeholder="192.168.0.2, 192.168.0.3, 192.168.0.4" id="ip" name="ip" v-model="ip" required>
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
                'vmanageIP': '',
                'ip': '',
                'loading': false,
                'serial': [],
                'failed':[]
            }
        },
        computed: {
            ips() {
                return this.ip.replace(' ', '').split(',')
            }
        },
        methods: {
            fetchSerial() {
                return axios.post('/activate/list', {
                    vmanageIP: this.vmanageIP
                })
            },
            activateDevice(target, i) {
                return axios.post('/activate', {
                    ip: target,
                    uuid: this.serial[i]['uuid'],
                    token: this.serial[i]['token']
                })
            },
            onSubmit() {
                this.failed = []
                this.loading = true
                this.fetchSerial()
                    .then(response => {
                        this.serial = response.data.vedgelist
                        const promises = this.ips.map((ip, i) => this.activateDevice(ip, i))
                        Promise.all(promises)
                            .then(responses => {
                                responses.forEach(response => {
                                    const r1 = response.data.r1
                                    const rcode1 = response.data.r1[response.data.r1.length - 1]
                                    if (rcode1 === '0') {
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
                    })
                    .catch(error => console.log(error))
            }
        }
    }
</script>
