Vue.component('Print', {
	template: `
		<el-drawer title="打印票据"  direction="rtl" size="1200px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-button style="margin-left: 180mm;margin-bottom: 10px;" @click="printPage">打印票据</el-button>
			<iframe id="PrintPath" :src="jump" frameborder="0" width="100%" height="800px"></iframe>
			</div>
		</el-drawer>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			jump:''
		}
	},
	methods: {
		open(){
			let query = {}
			Object.assign(query, {dialogstate:1})
			Object.assign(query, {print_ys:this.info.print_ys})
			Object.assign(query, {pjlx_id:this.info.pjlx_id})

			if (this.info.syt_id != '' || this.info.syt_id != undefined){
				Object.assign(query, {syt_id:this.info.syt_id})
			}

			if (this.info.ids != '' || this.info.ids != undefined){
				Object.assign(query, {ids:this.info.ids})
			}

			if (this.info.member_id != '' || this.info.member_id != undefined){
				Object.assign(query, {member_id:this.info.member_id})
			}

			this.jump = '/shop/Syt/'+this.info.funame+'?'+ param(query)
		},
		printPage() {
			var bdhtml = window.document.body.innerHTML;
			document.getElementById('PrintPath').focus();
			document.getElementById('PrintPath').contentWindow.focus();
			document.getElementById('PrintPath').contentWindow.print();
			//window.document.body.innerHTML = bdhtml; //重新给页面内容赋值；
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
