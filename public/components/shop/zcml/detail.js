Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">资产名称：</td>
						<td>
							{{form.zcml_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">资产编码：</td>
						<td>
							{{form.zcml_bm}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">资产性质：</td>
						<td>
							<span v-if="form.zcml_type == '1'">低值易耗</span>
							<span v-if="form.zcml_type == '2'">固定资产</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">添加时间：</td>
						<td>
							{{parseTime(form.zcml_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">资产照片：</td>
						<td>
						<div v-if="form.zcml_pic && form.zcml_pic.indexOf('[{') != -1" class="demo-image__preview">
							<el-image style="margin-right:5px" v-for="(item,i) in JSON.parse(form.zcml_pic)"  class="table_list_pic" :src="item.url" :key="i"  :preview-src-list="[item.url]"></el-image>
						</div>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">资产附件：</td>
						<td>
						<div v-if="form.zcml_fj && form.zcml_fj.indexOf('[{') != -1">
							<el-link style="margin-right:5px; font-size:13px" v-for="(item,i) in JSON.parse(form.zcml_fj)" target="_blank" :href="item.url"  :key="i">下载附件{{i+1}}</el-link>
						</div>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">资产所属：</td>
						<td>
							{{form.zclb_fid}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属分类：</td>
						<td>
							{{form.zclb_id}}
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
			axios.post(base_url+'/Zcml/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
