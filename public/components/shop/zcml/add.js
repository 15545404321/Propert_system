Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产名称" prop="zcml_name">
							<el-input  v-model="form.zcml_name" autoComplete="off" clearable  placeholder="请输入资产名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产编码" prop="zcml_bm">
							<el-input  v-model="form.zcml_bm" autoComplete="off" clearable  placeholder="请输入资产编码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产性质" prop="zcml_type">
							<el-radio-group v-model="form.zcml_type">
								<el-radio :label="1">低值易耗</el-radio>
								<el-radio :label="2">固定资产</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="添加时间" prop="zcml_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.zcml_time" clearable placeholder="请输入添加时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产照片" prop="zcml_pic">
							<Upload v-if="show" size="small"    file_type="images" :images.sync="form.zcml_pic"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产附件" prop="zcml_fj">
							<Upload size="small" file_type="files"    :files.sync="form.zcml_fj"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产介绍" prop="zcml_neirong">
							<el-input  type="textarea" autoComplete="off" v-model="form.zcml_neirong"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入资产介绍"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产所属" prop="zclb_fid">
							<el-select @change="selectZclb_id"  style="width:100%" v-model="form.zclb_fid" filterable clearable placeholder="请选择资产所属">
								<el-option v-for="(item,i) in zclb_fids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属分类" prop="zclb_id">
							<el-select   style="width:100%" v-model="form.zclb_id" filterable clearable placeholder="请选择所属分类">
								<el-option v-for="(item,i) in zclb_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
	},
	data(){
		return {
			form: {
				shop_id:'',
				xqgl_id:'',
				zcml_name:'',
				zcml_bm:'',
				zcml_type:1,
				zcml_time:curentTime(),
				zcml_pic:[],
				zcml_fj:[],
				zcml_neirong:'',
				zclb_fid:'',
				zclb_id:'',
			},
			zclb_fids:[],
			zclb_ids:[],
			loading:false,
			rules: {
				zcml_type:[
					{required: true, message: '资产性质不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Zcml/getFieldList').then(res => {
					if(res.data.status == 200){
						this.zclb_fids = res.data.data.zclb_fids
					}
				})
			}
		}
	},
	methods: {
		open(){
		},
		selectZclb_id(val){
			this.form.zclb_id = ''
			axios.post(base_url + '/Zcml/getZclb_id',{zclb_fid:val}).then(res => {
				if(res.data.status == 200){
					this.zclb_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Zcml/add',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
