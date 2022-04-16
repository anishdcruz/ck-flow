<template>
    <div>
        <x-button icon="ios-download" size="sm"
            :disabled="disabled" @click="toggleExport">
            {{$t('export')}}
        </x-button>
        <x-modal v-if="show" width="400" @close="toggleExport">
            <div slot="title">{{$t('export')}} ({{total}}) {{$t('entries')}}</div>
            <x-row>
                <x-col span="12">
                    <a :href="url" @click="toggleExport" :disabled="showForm" class="export-link">
                        <i class="icon icon-ios-download"></i>
                        <span>{{$t('download')}}</span>
                    </a>
                </x-col>
                <x-col span="12">
                    <button @click="toggleRecurring" class="export-link">
                        <i class="icon icon-clock"></i>
                        <span>{{$t('recurring')}}</span>
                    </button>
                </x-col>
            </x-row>
            <div class="export-recurring" v-if="showForm">
                <div class="form-group">
                    <label class="form-label">{{$t('email_to')}}</label>
                    <x-input v-model="form.email_to" />
                    <small class="error-control" v-if="errors.email_to">
                        {{errors.email_to[0]}}
                    </small>
                </div>
                <x-row>
                    <x-col span="8">
                        <div class="form-group">
                            <label class="form-label">{{$t('frequency')}}</label>
                            <select class="form-input" v-model="form.frequency">
                                <option value="daily">{{$t('daily')}}</option>
                                <option value="weekly">{{$t('weekly')}}</option>
                                <option value="monthly">{{$t('monthly')}}</option>
                            </select>
                            <small class="error-control" v-if="errors.frequency">
                                {{errors.frequency[0]}}
                            </small>
                        </div>
                    </x-col>
                    <x-col span="8" v-if="form.frequency === 'weekly'">
                        <div class="form-group">
                            <label class="form-label">{{$t('send_on')}}</label>
                            <select class="form-input" v-model="form.send_on">
                                <option value="1">{{$t('monday')}}</option>
                                <option value="2">{{$t('tuesday')}}</option>
                                <option value="3">{{$t('wednesday')}}</option>
                                <option value="4">{{$t('thursday')}}</option>
                                <option value="5">{{$t('friday')}}</option>
                                <option value="6">{{$t('saturday')}}</option>
                                <option value="7">{{$t('sunday')}}</option>
                            </select>
                            <small class="error-control" v-if="errors.send_on">
                                {{errors.send_on[0]}}
                            </small>
                        </div>
                    </x-col>
                    <x-col span="8" v-else-if="form.frequency === 'monthly'">
                        <div class="form-group">
                            <label class="form-label">{{$t('send_at')}}</label>
                            <select class="form-input" v-model="form.send_on">
                                <option value="1">1st</option>
                                <option value="2">2nd</option>
                                <option value="3">3rd</option>
                                <option value="4">4th</option>
                                <option value="5">5th</option>
                                <option value="6">6th</option>
                                <option value="7">7th</option>
                                <option value="8">8th</option>
                                <option value="9">9th</option>
                                <option value="10">10th</option>
                                <option value="11">11th</option>
                                <option value="12">12th</option>
                                <option value="13">13th</option>
                                <option value="14">14th</option>
                                <option value="15">15th</option>
                                <option value="16">16th</option>
                                <option value="17">17th</option>
                                <option value="18">18th</option>
                                <option value="19">19th</option>
                                <option value="20">20th</option>
                                <option value="21">21st</option>
                                <option value="22">22nd</option>
                                <option value="23">23rd</option>
                                <option value="24">24th</option>
                                <option value="25">25th</option>
                                <option value="26">26th</option>
                                <option value="27">27th</option>
                                <option value="28">28th</option>
                            </select>
                            <small class="error-control" v-if="errors.send_on">
                                {{errors.send_on[0]}}
                            </small>
                        </div>
                    </x-col>

                    <x-col span="8">
                        <div class="form-group">
                            <label class="form-label">{{$t('send_at')}}</label>
                            <select class="form-input" v-model="form.send_at">
                                <option value="1:00">1:00 {{$t('am')}}</option>
                                <option value="1:30">1:30 {{$t('am')}}</option>
                                <option value="2:00">2:00 {{$t('am')}}</option>
                                <option value="2:30">2:30 {{$t('am')}}</option>
                                <option value="3:00">3:00 {{$t('am')}}</option>
                                <option value="3:30">3:30 {{$t('am')}}</option>
                                <option value="4:00">4:00 {{$t('am')}}</option>
                                <option value="4:30">4:30 {{$t('am')}}</option>
                                <option value="5:00">5:00 {{$t('am')}}</option>
                                <option value="5:30">5:30 {{$t('am')}}</option>
                                <option value="6:00">6:00 {{$t('am')}}</option>
                                <option value="6:30">6:30 {{$t('am')}}</option>
                                <option value="7:00">7:00 {{$t('am')}}</option>
                                <option value="7:30">7:30 {{$t('am')}}</option>
                                <option value="8:00">8:00 {{$t('am')}}</option>
                                <option value="8:30">8:30 {{$t('am')}}</option>
                                <option value="9:00">9:00 {{$t('am')}}</option>
                                <option value="9:30">9:30 {{$t('am')}}</option>
                                <option value="10:00">10:00 {{$t('am')}}</option>
                                <option value="10:30">10:30 {{$t('am')}}</option>
                                <option value="11:00">11:00 {{$t('am')}}</option>
                                <option value="11:30">11:30 {{$t('am')}}</option>
                                <option value="12:00">12:00 {{$t('pm')}}</option>
                                <option value="12:30">12:30 {{$t('pm')}}</option>
                                <option value="13:00">1:00 {{$t('pm')}}</option>
                                <option value="13:30">1:30 {{$t('pm')}}</option>
                                <option value="14:00">2:00 {{$t('pm')}}</option>
                                <option value="14:30">2:30 {{$t('pm')}}</option>
                                <option value="15:00">3:00 {{$t('pm')}}</option>
                                <option value="15:30">3:30 {{$t('pm')}}</option>
                                <option value="16:00">4:00 {{$t('pm')}}</option>
                                <option value="16:30">4:30 {{$t('pm')}}</option>
                                <option value="17:00">5:00 {{$t('pm')}}</option>
                                <option value="17:30">5:30 {{$t('pm')}}</option>
                                <option value="18:00">6:00 {{$t('pm')}}</option>
                                <option value="18:30">6:30 {{$t('pm')}}</option>
                                <option value="19:00">7:00 {{$t('pm')}}</option>
                                <option value="19:30">7:30 {{$t('pm')}}</option>
                                <option value="20:00">8:00 {{$t('pm')}}</option>
                                <option value="20:30">8:30 {{$t('pm')}}</option>
                                <option value="21:00">9:00 {{$t('pm')}}</option>
                                <option value="21:30">9:30 {{$t('pm')}}</option>
                                <option value="22:00">10:00 {{$t('pm')}}</option>
                                <option value="22:30">10:30 {{$t('pm')}}</option>
                                <option value="23:00">11:00 {{$t('pm')}}</option>
                                <option value="23:30">11:30 {{$t('pm')}}</option>
                            </select>
                            <small class="error-control" v-if="errors.send_at">
                                {{errors.send_at[0]}}
                            </small>
                        </div>
                    </x-col>
                </x-row>
                <div class="form-group">
                    <label class="form-label">{{$t('export_name')}}</label>
                    <x-input v-model="form.name" />
                    <small class="error-control" v-if="errors.name">
                        {{errors.name[0]}}
                    </small>
                </div>
            </div>
            <template slot="footer">
                <div></div>
                <div>
                    <x-button @click="toggleExport">{{$t('cancel')}}</x-button>
                    <x-button  v-if="showForm" :loading="isProcessing" type="primary" @click="onSave">
                        {{this.$t('create')}}
                    </x-button>
                </div>
            </template>
        </x-modal>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    export default {
        props: {
            total: Number,
            resource: String,
            appliedFilters: Array,
            query: Object,
            disabled: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                show: false,
                showForm: false,
                isProcessing: false,
                form: {},
                errors: {},
                url: ''
            }
        },
        mounted() {
            this.setData()
        },
        methods: {
            setData() {
                this.showForm = false
                this.isProcessing = false
                Vue.set(this.$data, 'form', {
                    email_to: '',
                    frequency: 'weekly',
                    send_on: 1,
                    send_at: '9:00',
                    name: ''
                })
            },
            getParams() {
              const f = {}

              this.appliedFilters.forEach((filter, i) => {
                  f[`f[${i}][column]`] = filter.column.name
                  f[`f[${i}][operator]`] = filter.operator.name

                  if(filter.query_1 && Array.isArray(filter.query_1)) {
                    const list = filter.query_1.map((item) => {
                      return item.value
                    })

                    f[`f[${i}][query_1]`] = list.join(',,')
                  } else {
                    f[`f[${i}][query_1]`] = filter.query_1
                  }

                  f[`f[${i}][query_2]`] = filter.query_2
              })

              let params = {
                  ...this.query,
                  ...f
              }

              return params
            },
            toggleRecurring() {
                this.showForm = !this.showForm
            },
            toggleExport() {
                this.show = !this.show
                if(this.show) {
                    let params = this.getParams()

                    let q = Object.keys(params).map(function(k) {
                        return encodeURIComponent(k) + "=" + encodeURIComponent(params[k]);
                    }).join('&')

                    this.url = `/api/exports/${this.resource}?${q}`
                }
            },
            onSave() {
                this.errors = {}
                this.isProcessing = true
                const f = []
                this.appliedFilters.forEach((filter, i) => {
                    const c = {
                        column: filter.column.name,
                        operator: filter.operator.name,
                        query_2: filter.query_2
                    }
                    if(filter.query_1 && Array.isArray(filter.query_1)) {
                      const list = filter.query_1.map((item) => {
                        return item.value
                      })

                      c.query_1 = list.join(',,')
                    } else {
                      c.query_1 = filter.query_1
                    }
                    f.push(c)
                })


                let form = {
                    ...this.query,
                    ...this.form,
                    f
                }

                this.$http.post(`/api/recurring_exports?resource=${this.resource}`, form)
                    .then(res => {
                        if(res.data.saved) {
                            this.$message.success(`${this.$t('saved_success')}`)
                            this.toggleRecurring()
                            this.toggleExport()
                        }
                    })
                    .catch(error => {
                        this.isProcessing = false
                        if(error.response.status === 422) {
                            this.errors = error.response.data.errors
                        }
                        this.$message.error(error.response.data.message)
                    })
            },
        }
    }
</script>
