Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">票据名称：</td>
						<td>
							{{form.pjgl_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">标题设置：</td>
						<td>
							{{form.pjmb_title}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">纸张宽度：</td>
						<td>
							{{form.pjmb_kuan}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">纸张高度：</td>
						<td>
							{{form.pjmb_gao}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">公章打印：</td>
						<td>
							<span v-if="form.pjgl_gzdy == '1'">不打印</span>
							<span v-if="form.pjgl_gzdy == '2'">打印</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">公章上传：</td>
						<td>
							<el-image v-if="form.pjgl_gongzhang" class="table_list_pic" :src="form.pjgl_gongzhang"  :preview-src-list="[form.pjgl_gongzhang]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">水平位置：</td>
						<td>
							{{form.pimb_gzwz}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
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
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Pjmb/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
