<template>
    <v-layout row wrap>
        <v-flex xs5 mr-5>
            <v-card>
                <v-container fill-height fluid pa-2>
                    <v-textarea v-model="source.text" name="source-text" label="Source text" rows="12"></v-textarea>
                </v-container>
                <v-card-actions>
                    <v-select :items="availableLanguages"
                              v-model="source.language"
                              item-value="code"
                              item-text="title"
                              label="Choose language (auto-select)"
                              prepend-icon="language"
                              solo ></v-select>
                    <v-spacer></v-spacer>
                    <v-progress-circular indeterminate color="primary" v-show="loading"></v-progress-circular>&nbsp;
                    <v-btn color="primary" v-on:click="translate" :disabled=translateIsDisabled>
                        <span>Translate!</span>
                    </v-btn>
                </v-card-actions>
                <v-divider></v-divider>
                <v-container pa-2>
                    <v-chip color="green" text-color="white" small
                            v-for="flag in activeSourceFlags" :key="flag.name">
                        <strong>
                            {{ flag.name }}<span v-if="flag.value">={{ flag.value }}</span>
                        </strong>
                    </v-chip>
                </v-container>
            </v-card>
        </v-flex>
        <v-flex xs5>
            <v-card>
                <v-container fill-height fluid pa-2>
                    <v-textarea v-model="target.text" name="source-text" label="Translated text" rows="12" readonly></v-textarea>
                </v-container>
                <v-card-actions>
                    <v-select :items="availableLanguages"
                              v-model="target.language"
                              item-value="code"
                              item-text="title"
                              label="Choose language (auto-select)"
                              prepend-icon="language"
                              solo ></v-select>
                    <v-spacer></v-spacer>
                </v-card-actions>
                <v-divider></v-divider>
                <v-container pa-2>
                    <v-chip color="green" text-color="white" small
                            v-for="flag in activeTargetFlags" :key="flag.name">
                        <strong>{{ flag.name }}</strong>
                        <span v-if="flag.value">{{ flag.value }}</span>
                    </v-chip>
                </v-container>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
import _ from 'lodash'

export default {
    name: 'translation-new',
    data: () => ({
        availableLanguages: [
            { code: 'en', title: 'English', },
            { code: 'de', title: 'German', },
            { code: 'fr', title: 'French', },
            { code: 'es', title: 'Spanish', },
            { code: 'pt', title: 'Portuguese', },
            { code: 'it', title: 'Italian', },
            { code: 'nl', title: 'Dutch', },
            { code: 'pl', title: 'Polish', },
            { code: 'ru', title: 'Russian', },
        ],
        source: {
            language: null,
            text: null,
            flags: {
                auto_detected_language: { name: "detected_language", active: false, value: "" }
            }
        },
        target: {
            language: null,
            text: null,
            flags: {
                loaded_from_cache: { name: "loaded_from_cache", active: false }
            }
        },
        loading: false
    }),
    computed: {
        translateIsDisabled: function() {
            return !this.source.text || this.loading;
        },
        activeSourceFlags() {
            return _.filter(this.source.flags, function (o) { return o.active })
        },
        activeTargetFlags() {
            return _.filter(this.target.flags, function (o) { return o.active })
        }
    },
    methods: {
        translate: function () {
            let url = '/translate/text/' + this.source.text;

            if(this.target.language !== null) {
                url += '/target-lang/' + this.target.language
            }
            if(this.source.language !== null) {
                url += '/source-lang/' + this.source.language
            }

            this.$api.interceptors.request.use((config) => {
                this.loading = true
                return config
            });

            this.$api.interceptors.response.use((config) => {
                this.loading = false
                return config
            });

            this.$api.get(url).then((response) => {
                if(response.status === 200) {
                    this.target.text = response.data.data !== null ? response.data.data.target_text : ''
                    this.target.flags.loaded_from_cache.active = response.data.loaded_from_cache
                    this.source.flags.auto_detected_language.active = true
                    this.source.flags.auto_detected_language.value = response.data.data.source_language
                }
            }, (error) => {
                console.log(error)
            })
        }
    }
}
</script>
