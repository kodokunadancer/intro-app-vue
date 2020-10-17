<template>
  <div class="edit-profile">
    <!-- ローディング -->
    <div class="loading" v-show="loading">
      <Loader>ロード中・・・</Loader>
    </div>
    <!-- フォーム -->
    <form class="form" v-if="!loading" @submit.prevent="submit">
      <div class="tittle">プロフィール編集ページ</div>
      <!-- 送信エラー -->
      <div class="errors" v-if="errors">
        <ul class="errors__list" v-if="errors.photo">
          <li class="errors__item" v-for="msg in errors.photo" :key="msg">{{ msg }}</li>
        </ul>
        <ul class="errors__list" v-if="errors.textName">
          <li class="errors__item" v-for="msg in errors.textName" :key="msg">{{ msg }}</li>
        </ul>
        <ul class="errors__list" v-if="errors.textIntroduction">
          <li class="errors__item" v-for="msg in errors.textIntroduction" :key="msg">{{ msg }}</li>
        </ul>
      </div>
      <!-- ファイル選択エラー -->
      <div class="errors" v-if="fileErrors.length">
        <ul class="errors__list">
          <li class="errors__item" v-for="msg in fileErrors" :key="msg">{{ msg }}</li>
        </ul>
      </div>
      <div class="form-photo">
        <!-- 画像選択 -->
        <input id="edit-profile-input" class="form-photo__input" type="file" name="photo" @change="onFileChange">
        <!-- プレビュー -->
        <div class="form-photo__output--true" v-if="preview">
          <img :src="preview">
        </div>
        <div class="form-photo__output--false"  v-else>
          <div class="form-photo-img" v-if="myProfile.photos[0]">
            <img :src="myProfile.photos[0].url">
          </div>
          <div class="form-photo-img" v-else>
            <img src="https://introductionapp.s3-ap-northeast-1.amazonaws.com/vue/profile_img.png">
          </div>
        </div>
      </div>
      <div class="form-text">
        <label class="form-text__label" for="name">名前</label>
        <input class="form-text__input" type="text" id="name" name="name" v-model="text.name">
        <label class="form-text__label" for="introduction">自己紹介</label>
        <textarea class="form-text__textarea" type="text" id="introduction" name="introduction" v-model="text.introduction"></textarea>
      </div>
      <!-- 完了ボタン -->
      <div class="btn-group">
        <button class="cancel" @click="cancel">キャンセル</button>
        <button class="submit" type="submit">完了</button>
      </div>
    </form>
  </div>
</template>

<script>

import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util.js'
import Loader from './Loader.vue'
import { mapState } from 'vuex'

export default {

  components: {
    Loader
  },
  data() {
    return {
      preview: null,
      photo: null,
      text: {
        name: `${ this.myProfile.name}`,
        introduction: `${ this.myProfile.introduction }`
      },
      errors: null,
      fileErrors: [],
      loading: false,
    }
  },
  props: {
    id: {
      type: Number,
      required: true
    },
    myProfile: {
      type: Object,
      required: true
    }
  },
  computed: {
    noEdit() {
      return this.photo === null && this.text.name === `${ this.myProfile.name }` && this.text.introduction === `${ this.myProfile.introduction }`
    }
  },
  methods: {
    //フォームでファイルが選択されたら実行
    onFileChange(event) {

      //ファイルに関するエラーメッセージをクリア
      this.fileErrors.length = 0

      //何も選択されていなかったら処理中断
      if(event.target.files.length === 0) {
        //ファイル入力欄の値とプレビューの値をクリアする
        this.reset()
        return false
      }
      //選択されたファイルが画像で無い場合、処理中断
      if(! event.target.files[0].type.match('image.*')) {
        //ファイル入力欄の値とプレビューの値をクリアする
        this.reset()
        return false
      }

      //FileReaderクラスのインスタンスを取得
      const reader = new FileReader()

      //ファイルを読み込み終わったタイミングで実行する処理
      reader.onload = e => {
        //previewにはデータURLが代入される
        this.preview = e.target.result
      }

      //ファイルを選択した時点でバリデーションをかける
      this.checkFile(event)

      //ファイルデータを読み込み、DataURLを取得
      reader.readAsDataURL(event.target.files[0])
      //サーバーへ送信する用のファイルデータを格納
      this.photo = event.target.files[0]
    },

    checkFile(event) {
      //選択されたファイルの拡張子を取得
      var filename = event.target.files[0].name
      var position = filename.lastIndexOf('.')
      var extension = filename.slice(position + 1)
      //拡張子を小文字に変換
      var extLowerCase = extension.toLowerCase()
      //使用できる拡張子を配列に格納
      const extensions = new Array('jpg','jpeg','png','gif','heic')
      const limit = 2000000
      //ファイルサイズの定義
      var fileSize = event.target.files[0].size
      if(extLowerCase === 'heic') {
        this.fileErrors.push('拡張子HEICはPC上では扱えません（スマホでは扱えます）')
      }
      if (extensions.indexOf(extLowerCase) === -1) {
        this.fileErrors.push('ファイルの拡張子は「jpg,jpeg,png,gif,heic」しか使用できません')
      }
      if (fileSize > limit) {
        this.fileErrors.push('ファイルサイズが大きすぎる可能性があります')
      }
    },
    //ファイル入力欄の値とプレビューの値と選択中の写真データとをクリアするメソッド
    reset() {
      //送信が完了したら、入力欄とプレビューをクリア
      window:onload = () => {
        this.preview = ''
        this.photo = null
        this.$el.querySelector('#edit-profile-input').value = null
        this.extensionErrors = null
      }
    },

    // 入力されたファイル情報をAjax方式でサーバー側へ送り保存し、入力欄とプレビューをクリアし、写真フォームを非表示にする
    async submit() {

      //何も編集をせず完了ボタンを押した場合
      if(this.noEdit) {
        this.$store.commit('message/setSuccessContent', {
          successContent: "編集は行われませんでした",
          timeout: 6000
        })
        this.cancel()
        return false
      }

      //ローディング状態にする
      this.loading = true

      //以下は慣用的なFormDataのユースケース（活用事例）
      const formData = new FormData()
      //画像が選択されていない場合、サーバーへnullを送らせない
      if(this.photo) {
        formData.append('photo', this.photo)
      }
      formData.append('textName', this.text.name)
      formData.append('textIntroduction', this.text.introduction)

      const response = await axios.post(`/api/mypage/${ this.id }/myprofile/edit`, formData)

      // 通信が終わったらローディング状態を解除する
      this.loading = false

      //バリデーションエラーの場合は、エラーメッセージを表示する関係から、入力欄やプレビューのリセットの前にリターンする
      if(response.status === UNPROCESSABLE_ENTITY) {
        this.$store.commit('message/setErrorContent', {
          errorContent: "プロフィールの編集に失敗しました",
          timeout: 6000
        })
        this.errors = response.data.errors
        return false
      }

      //送信が完了したら、入力欄とプレビューをクリア
      this.reset()

      //500エラーなどの場合は、入力欄やプレビューをクリアし、写真選択フォームを閉じたあとに、エラーモジュールのステートにエラー内容をセットし、App.vueによって強制的に画面遷移させる
      if(response.status !== OK) {
        this.$store.commit('message/setErrorContent', {
          errorContent: "プロフィールの編集に失敗しました",
          timeout: 6000
        })
        this.$store.commit('error/setCode', response.status)
        return false
      }

      //サーバーとの通信に成功した場合、一つ前のページへリダイレクトさせる
      this.cancel
      this.$store.commit('message/setSuccessContent', {
        successContent: "プロフィールの編集に成功しました",
        timeout: 6000
      })
    },
    //キャンセルボタンをクリックしたときなどに発火
    cancel() {
      this.reset()
      this.$router.push(`${ this.$store.state.route.prevRoute.path}`)
    }
  }
}
</script>
