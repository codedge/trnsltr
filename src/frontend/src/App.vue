<template>
  <v-app>
    <v-toolbar app>
      <v-toolbar-title class="headline text-uppercase">
        <span>trnsltr</span>
        <span class="font-weight-light">
          <small>powered by DeepL</small>
        </span>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn flat :to="{name:'list-translation'}">
        <v-icon dark>list</v-icon> Translations
      </v-btn>
      <v-btn color="info" :to="{name:'new-translation'}">
        <v-icon dark>add</v-icon> Translate
      </v-btn>
    </v-toolbar>

    <v-content>
      <router-view/>
    </v-content>

    <v-footer height="auto" color="darken-1">
      <v-layout justify-center row wrap>
        <v-flex py-3 text-xs-center xs12 class="grey">
          Coded with <v-icon color="red darken-4">favorite</v-icon> in Germany.
        </v-flex>
      </v-layout>
    </v-footer>
  </v-app>

</template>

<script>

export default {
  name: 'App',
  components: {

  },
  data () {
    return {
      //
    }
  }
}
</script>
