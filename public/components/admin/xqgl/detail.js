Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">项目名称：</td>
						<td>
							{{form.xqgl_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">管理面积：</td>
						<td>
							{{form.xqgl_glmj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房间数量：</td>
						<td>
							{{form.xqgl_fjsl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">车位数量：</td>
						<td>
							{{form.xqgl_cwsl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">车辆数量：</td>
						<td>
							{{form.xqgl_clsl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">月应收费：</td>
						<td>
							{{form.xqgl_yysf}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">年应收费：</td>
						<td>
							{{form.xqgl_nysf}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">省份市区：</td>
						<td>
							{{form.xqgl_address}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">经度纬度：</td>
						<td>
							{{form.xggl_jdwd}}
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
			axios.post(base_url+'/Xqgl/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
