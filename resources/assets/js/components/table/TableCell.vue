<script type="text/javascript">
  export default {
    name: 'XTd',
    render: function (createElement) {
      return createElement(
        this.type,   // tag name
        {
          class: [
            this.size ? `w-${this.size}` : null
          ],
          style: {
            'text-align': this.align
          }
        },
        this.$slots.default // array of children
      )
    },
    props: {
      size: {
        default: null
      },
      type: {
        default: 'td'
      },
      align: {
        default: 'left'
      }
    }
  }
</script>
