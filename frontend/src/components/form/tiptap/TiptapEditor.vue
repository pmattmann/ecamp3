<script>

import { Editor, EditorContent, EditorMenuBubble } from 'tiptap'
import {
  BulletList,
  HardBreak,
  Heading,
  ListItem,
  OrderedList,
  Bold,
  Italic,
  Strike,
  Underline,
  History
} from 'tiptap-extensions'
import { VBtn, VIcon, VItemGroup, VSpacer, VToolbar, VTooltip, VOverflowBtn } from 'vuetify/lib'

export default {
  name: 'TiptapEditor',
  components: {
    VBtn,
    VIcon,
    VItemGroup,
    VSpacer,
    VToolbar,
    EditorContent,
    EditorMenuBubble
  },
  props: {
    value: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      emitAfterOnUpdate: false,
      editor: new Editor({
        extensions: [
          new History(),
          new Bold(),
          new Italic(),
          new Underline(),
          new Strike(),
          new ListItem(),
          new BulletList(),
          new OrderedList(),
          new Heading({ levels: [1, 2, 3] }),
          new HardBreak()
        ],
        content: this.value,
        onUpdate: this.onUpdate,
        onFocus: this.onFocus,
        onBlur: this.onBlur
      }),
      regex: {
        emptyParagraph: new RegExp('<p></p>'),
        lineBreak1: new RegExp('<br>', 'g'),
        lineBreak2: new RegExp('<br/>', 'g')
      }
    }
  },
  watch: {
    value (val) {
      if (this.emitAfterOnUpdate) {
        this.emitAfterOnUpdate = false
        return
      }
      this.editor.setContent(val)
    }
  },
  methods: {
    focus () {
      this.editor.focus()
    },
    onUpdate (info) {
      let output = info.getHTML()

      // Replace some Tags, to be compatible with backend HTMLPurifier
      output = output.replace(this.regex.emptyParagraph, '')
      output = output.replace(this.regex.lineBreak1, '<br />')
      output = output.replace(this.regex.lineBreak1, '<br />')

      this.emitAfterOnUpdate = true
      this.$emit('input', output, info)
    },
    onFocus (e) {
      this.$emit('focus', e)
    },
    onBlur (e) {
      this.$emit('blur', e)
    },
    getContent () {
      return [
        this.genToolbar(),
        this.genEditorContent()
      ]
    },
    genToolbar () {
      return this.$createElement(EditorMenuBubble, {
        props: {
          editor: this.editor
        },
        scopedSlots: {
          default: (props) => {
            return this.$createElement('div', {
              staticClass: 'menububble',
              class: { 'is-active': props.menu.isActive },
              style: {
                left: props.menu.left + 'px',
                bottom: props.menu.bottom + 'px'
              }
            }, [
              this.$createElement(VToolbar, {
                props: { dense: true, rounded: true },
                staticClass: 'v-toolbar--narrow'
              }, [
                this.genToolbarItem(props.isActive.bold(), props.commands.bold, 'mdi-format-bold', this.$t('global.wysiwyg.bold')),
                this.genToolbarItem(props.isActive.italic(), props.commands.italic, 'mdi-format-italic', this.$t('global.wysiwyg.italic')),
                this.genToolbarItem(props.isActive.underline(), props.commands.underline, 'mdi-format-underline', this.$t('global.wysiwyg.underline')),
                this.genToolbarItem(props.isActive.strike(), props.commands.strike, 'mdi-format-strikethrough', this.$t('global.wysiwyg.strike')),
                this.$createElement(VOverflowBtn, {
                  style: {
                    width: '180px'
                  },
                  props: {
                    hideDetails: true,
                    text: true,
                    dense: true,
                    label: this.$t('global.wysiwyg.level.name'),
                    items: [
                      {
                        text: this.$t('global.wysiwyg.level.1'),
                        value: 1
                      },
                      {
                        text: this.$t('global.wysiwyg.level.2'),
                        value: 2
                      },
                      {
                        text: this.$t('global.wysiwyg.level.3'),
                        value: 3
                      },
                      {
                        text: this.$t('global.wysiwyg.level.0'),
                        value: 0
                      }
                    ],
                    value: props.isActive.paragraph() ? 0 : [1, 2, 3].find(i => props.isActive.heading({ level: i }))
                  },
                  on: {
                    change: (value) => {
                      if (value === 0) {
                        props.commands.paragraph()
                      } else {
                        props.commands.heading({ level: value })
                      }
                    }
                  }
                })
              ])
            ])
          }
        }
      })
    },
    genToolbarItem (isActive, onClick, icon, label) {
      return this.$createElement(VTooltip, {
        props: {
          top: true
        },
        scopedSlots: {
          activator: ({ on, attrs }) => {
            return this.$createElement(VBtn, {
              staticClass: 'ml-1 px-2 editbar__btn',
              class: {
                'v-btn--active': isActive
              },
              props: {
                text: true,
                tile: true
              },
              attrs: attrs,
              on: Object.assign(on, { click: onClick })
            }, [
              this.$createElement(VIcon, {}, [icon])
            ])
          }
        }
      }, label)
    },
    genEditorContent () {
      // TODO: Emit MouseDown/Up events
      return this.$createElement(EditorContent, {
        staticClass: 'editor__content pt-2',
        props: {
          editor: this.editor
        }
      })
    }
  },
  render (h) {
    return h('div', {
      staticClass: 'editor py-2'
    }, this.getContent())
  }
}
</script>

<style scoped lang="scss">

  .v-text-field--filled:not(.v-text-field--single-line) div.editor {
    margin-top: 10px;
  }

  div.editor ::v-deep .ProseMirror {
    border: 0 !important;
    box-shadow: none !important;
    outline: none;
    color: rgba(0, 0, 0, 0.87);
    line-height: normal !important;

    h1, h2, h3, p, ol, ul {
      margin-bottom: 6px;
    }

    h1 {
      margin-top: 18px;
    }

    h2 {
      margin-top: 15px;
    }

    h3 {
      margin-top: 12px;
    }

    :first-child {
      margin-top: 0;
    }

    li p {
      margin-bottom: 3px;
    }

    li p:not(:last-child) {
      margin-bottom: 0;
    }
  }

  div.editor ::v-deep .menububble {
    position: absolute;
    display: flex;
    z-index: 20;
    margin-bottom: .5rem;
    transform: translateX(-50%);
    visibility: hidden;
    opacity: 0;
    transition: opacity .2s, visibility .2s;

    &.is-active {
      opacity: 1;
      visibility: visible;
    }

    .v-select__selections {
      width: auto;
    }

    .v-select__selections input {
      min-width: 0;
    }

    .v-overflow-btn .v-input__slot {
      border: none;
    }
  }

  div.editor ::v-deep .v-toolbar--narrow .v-toolbar__content {
    padding: 0;
  }

  div.editor ::v-deep .editbar__btn {
    min-width: 48px;
  }

</style>
